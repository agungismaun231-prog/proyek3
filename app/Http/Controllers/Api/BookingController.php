<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BookingResource;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Rating;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class BookingController extends Controller
{
    /**
     * Get list of bookings scoped by user role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Booking::with(['service', 'technician', 'customer', 'invoice.payments', 'rating']);

        if ($user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer) {
                return response()->json(['bookings' => []], 200);
            }
            $query->where('customer_id', $customer->id);
        } elseif ($user->role === 'technician') {
            $technician = Technician::where('user_id', $user->id)->first()
                ?? Technician::where('email', $user->email)->first();

            if (!$technician) {
                return response()->json(['bookings' => []], 200);
            }
            $query->where('technician_id', $technician->id);
        }

        $bookings = $query->orderBy('scheduled_date', 'desc')->get();

        return response()->json([
            'bookings' => BookingResource::collection($bookings),
        ], 200);
    }

    /**
     * Create a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date',
            'service_location' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer = Customer::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'phone' => '',
                'address' => $validated['service_location'],
            ]
        );

        $service = Service::findOrFail($validated['service_id']);

        $booking = DB::transaction(function () use ($customer, $service, $validated) {
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'technician_id' => $validated['technician_id'] ?? null,
                'scheduled_date' => Carbon::parse($validated['scheduled_date']),
                'service_location' => $validated['service_location'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'total_price' => $service->price,
            ]);

            // Auto-generate invoice
            $subtotal = (float) $service->price;
            $tax = round($subtotal * 0.1, 2);
            $total = $subtotal + $tax;

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . sprintf('%04d', $booking->id),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'issued',
                'due_date' => Carbon::parse($validated['scheduled_date'])->addDays(3),
            ]);

            return $booking;
        });

        $booking->load(['service', 'technician', 'customer', 'invoice.payments', 'rating']);

        // Notifikasi untuk customer
Notification::create([
    'user_id' => $user->id,
    'title' => 'Booking Berhasil',
    'message' => 'Booking layanan berhasil dibuat.',
    'type' => 'booking',
    'is_read' => false,
]);

// Notifikasi untuk teknisi jika teknisi dipilih saat booking
if ($booking->technician_id) {
    $technician = Technician::find($booking->technician_id);

    if ($technician && $technician->user_id) {
        Notification::create([
            'user_id' => $technician->user_id,
            'title' => 'Booking Servis Baru',
            'message' => 'Ada booking servis baru yang ditugaskan kepada Anda. Silakan periksa jadwal dan detail booking.',
            'type' => 'booking',
            'is_read' => false,
        ]);
    }
}


        return response()->json([
            'booking' => new BookingResource($booking),
        ], 201);
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();
        $booking->loadMissing(['service', 'technician', 'customer', 'invoice.payments', 'rating']);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,en_route,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Authorization check
        if ($user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer || $booking->customer_id !== $customer->id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke booking ini.'], 403);
            }
            if ($validated['status'] !== 'cancelled') {
                return response()->json(['message' => 'Customer hanya dapat membatalkan booking.'], 403);
            }
        } elseif ($user->role === 'technician') {
            $technician = Technician::where('user_id', $user->id)->first()
                ?? Technician::where('email', $user->email)->first();

            if (!$technician || $booking->technician_id !== $technician->id) {
                return response()->json(['message' => 'Anda tidak ditugaskan pada booking ini.'], 403);
            }
        }

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'completed') {
            $updateData['completion_notes'] = $validated['notes'] ?? $booking->completion_notes ?? 'Pekerjaan selesai.';
            if ($booking->invoice && $booking->invoice->status === 'draft') {
                $booking->invoice->update(['status' => 'issued']);
            }
        } elseif ($validated['status'] === 'cancelled') {
            $updateData['cancellation_reason'] = $validated['notes'] ?? $booking->cancellation_reason ?? 'Dibatalkan oleh pengguna.';
        } elseif (!empty($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        $booking->update($updateData);

        return response()->json([
            'booking' => new BookingResource($booking->fresh(['service', 'technician', 'customer', 'invoice.payments', 'rating'])),
        ], 200);
    }

    /**
     * Submit rating and review for a completed booking.
     */
    public function rating(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();
        $booking->loadMissing(['customer', 'technician']);

        $customer = Customer::where('email', $user->email)->first();
        if (!$customer || $booking->customer_id !== $customer->id) {
            return response()->json(['message' => 'Hanya pelanggan dari pemesanan ini yang dapat memberi rating.'], 403);
        }

        if ($booking->status !== 'completed') {
            return response()->json(['message' => 'Rating hanya dapat diberikan untuk servis yang sudah selesai.'], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        Rating::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'customer_id' => $customer->id,
                'technician_id' => $booking->technician_id,
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? '',
            ]
        );

        $refreshed = $booking->fresh(['service', 'technician', 'customer', 'invoice.payments', 'rating']);

        return response()->json([
            'booking' => new BookingResource($refreshed),
        ], 200);
    }
}
