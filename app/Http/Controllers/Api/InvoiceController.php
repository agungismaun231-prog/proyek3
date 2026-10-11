<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvoiceResource;
use App\Http\Resources\Api\PaymentResource;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * List invoices scoped by user role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Invoice::with(['booking.service', 'booking.customer', 'payments']);

        if ($user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer) {
                return response()->json(['invoices' => []], 200);
            }
            $query->whereHas('booking', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            });
        } elseif ($user->role === 'technician') {
            $technician = Technician::where('user_id', $user->id)->first()
                ?? Technician::where('email', $user->email)->first();

            if (!$technician) {
                return response()->json(['invoices' => []], 200);
            }
            $query->whereHas('booking', function ($q) use ($technician) {
                $q->where('technician_id', $technician->id);
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'invoices' => InvoiceResource::collection($invoices),
        ], 200);
    }

    /**
     * List payment records for a specific invoice.
     */
    public function payments(Request $request, Invoice $invoice): JsonResponse
    {
        $user = $request->user();
        $invoice->loadMissing('booking.customer');

        if ($user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer || $invoice->booking->customer_id !== $customer->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $payments = $invoice->payments()->latest('created_at')->get();

        return response()->json([
            'payments' => PaymentResource::collection($payments),
        ], 200);
    }

    /**
     * Submit payment for an invoice.
     */
    public function submitPayment(Request $request, Invoice $invoice): JsonResponse
    {
        $user = $request->user();
        $invoice->loadMissing('booking.customer');

        if ($user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer || $invoice->booking->customer_id !== $customer->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,qris',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = 'payment-' . time() . '-' . uniqid('', true) . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('payments', $filename, 'public');
        }

        $payment = DB::transaction(function () use ($invoice, $validated, $proofPath) {
            return Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_gateway' => 'manual',
                'reference_number' => $validated['reference_number'] ?? null,
                'payment_proof' => $proofPath,
                'submitted_date' => now(),
                'status' => 'pending_approval',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return response()->json([
            'payment' => new PaymentResource($payment),
        ], 201);
    }
}
