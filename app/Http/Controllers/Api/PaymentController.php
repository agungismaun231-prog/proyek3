<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentResource;
use App\Models\Payment;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Approve a pending payment (Admin or assigned technician).
     */
    public function approve(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        $payment->loadMissing('invoice.booking');

        if ($user->role !== 'admin') {
            $technician = Technician::where('user_id', $user->id)->first()
                ?? Technician::where('email', $user->email)->first();

            if (!$technician || $payment->invoice->booking->technician_id !== $technician->id) {
                return response()->json([
                    'message' => 'Hanya admin atau teknisi terkait yang dapat menyetujui pembayaran.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($payment, $user, $validated) {
            $payment->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_date' => now(),
                'notes' => $validated['notes'] ?? $payment->notes ?? 'Approved',
            ]);

            $invoice = $payment->invoice;
            $approvedSum = $invoice->payments()->where('status', 'approved')->sum('amount');

            if ($approvedSum >= $invoice->total) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);
            }
        });

        return response()->json([
            'message' => 'Pembayaran berhasil disetujui.',
            'payment' => new PaymentResource($payment->fresh()),
        ], 200);
    }

    /**
     * Reject a pending payment.
     */
    public function reject(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        $payment->loadMissing('invoice.booking');

        if ($user->role !== 'admin') {
            $technician = Technician::where('user_id', $user->id)->first()
                ?? Technician::where('email', $user->email)->first();

            if (!$technician || $payment->invoice->booking->technician_id !== $technician->id) {
                return response()->json([
                    'message' => 'Hanya admin atau teknisi terkait yang dapat menolak pembayaran.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_date' => now(),
            'notes' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Pembayaran ditolak.',
            'payment' => new PaymentResource($payment->fresh()),
        ], 200);
    }
}
