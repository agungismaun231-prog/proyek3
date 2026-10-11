<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $serviceLocation = $this->service_location;
        if (empty($serviceLocation) && $this->relationLoaded('customer') && $this->customer) {
            $serviceLocation = $this->customer->address;
        }

        return [
            'id' => (int) $this->id,
            'status' => (string) ($this->status ?? 'pending'),
            'scheduled_date' => $this->scheduled_date ? $this->scheduled_date->toIso8601String() : null,
            'service_location' => (string) ($serviceLocation ?? ''),
            'notes' => (string) ($this->notes ?? ''),
            'completion_notes' => (string) ($this->completion_notes ?? ''),
            'cancellation_reason' => (string) ($this->cancellation_reason ?? ''),
            'total_price' => (float) ($this->total_price ?? 0),
            'service' => $this->relationLoaded('service') && $this->service
                ? (new ServiceResource($this->service))->toArray($request)
                : null,
            'technician' => $this->relationLoaded('technician') && $this->technician
                ? (new TechnicianResource($this->technician))->toArray($request)
                : null,
            'customer' => $this->relationLoaded('customer') && $this->customer ? [
                'id' => (int) $this->customer->id,
                'name' => (string) $this->customer->name,
                'phone' => (string) ($this->customer->phone ?? ''),
                'address' => (string) ($this->customer->address ?? ''),
                'email' => (string) ($this->customer->email ?? ''),
            ] : null,
            'invoice' => $this->relationLoaded('invoice') && $this->invoice
                ? (new InvoiceResource($this->invoice))->toArray($request)
                : null,
            'rating' => $this->relationLoaded('rating') && $this->rating ? [
                'id' => (int) $this->rating->id,
                'rating' => (int) $this->rating->rating,
                'review' => (string) ($this->rating->review ?? ''),
            ] : null,
        ];
    }
}
