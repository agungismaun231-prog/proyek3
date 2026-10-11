<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payments = $this->relationLoaded('payments') ? $this->payments : $this->payments()->get();
        $approvedAmount = (float) $payments->where('status', 'approved')->sum('amount');
        $pendingAmount = (float) $payments->where('status', 'pending_approval')->sum('amount');
        $total = (float) ($this->total ?? 0);
        $remainingAmount = max(0.0, $total - $approvedAmount);
        $hasPending = $payments->where('status', 'pending_approval')->isNotEmpty();
        $latestPaymentModel = $payments->sortByDesc('created_at')->first();

        return [
            'id' => (int) $this->id,
            'invoice_number' => (string) $this->invoice_number,
            'subtotal' => (float) ($this->subtotal ?? 0),
            'tax' => (float) ($this->tax ?? 0),
            'total' => $total,
            'status' => (string) ($this->status ?? 'draft'),
            'due_date' => $this->due_date ? $this->due_date->toIso8601String() : null,
            'booking_id' => (int) $this->booking_id,
            'booking' => $this->relationLoaded('booking') && $this->booking ? [
                'id' => (int) $this->booking->id,
                'status' => (string) $this->booking->status,
                'scheduled_date' => $this->booking->scheduled_date ? $this->booking->scheduled_date->toIso8601String() : null,
                'service_location' => (string) ($this->booking->service_location ?? ''),
                'service' => $this->booking->service ? [
                    'id' => (int) $this->booking->service->id,
                    'name' => (string) $this->booking->service->name,
                ] : null,
                'customer' => $this->booking->customer ? [
                    'id' => (int) $this->booking->customer->id,
                    'name' => (string) $this->booking->customer->name,
                    'phone' => (string) ($this->booking->customer->phone ?? ''),
                    'address' => (string) ($this->booking->customer->address ?? ''),
                ] : null,
            ] : null,
            'approved_amount' => round($approvedAmount, 2),
            'remaining_amount' => round($remainingAmount, 2),
            'pending_amount' => round($pendingAmount, 2),
            'has_pending_payment' => $hasPending,
            'latest_payment' => $latestPaymentModel ? (new PaymentResource($latestPaymentModel))->toArray($request) : null,
        ];
    }
}
