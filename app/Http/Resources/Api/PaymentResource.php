<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $proofUrl = '';
        if (!empty($this->payment_proof)) {
            $proofUrl = str_starts_with($this->payment_proof, 'http')
                ? $this->payment_proof
                : url(Storage::url($this->payment_proof));
        }

        return [
            'id' => (int) $this->id,
            'invoice_id' => (int) $this->invoice_id,
            'amount' => (float) ($this->amount ?? 0),
            'payment_method' => (string) ($this->payment_method ?? ''),
            'reference_number' => (string) ($this->reference_number ?? ''),
            'status' => (string) ($this->status ?? 'pending_approval'),
            'notes' => (string) ($this->notes ?? ''),
            'payment_proof' => (string) ($this->payment_proof ?? ''),
            'payment_proof_url' => (string) $proofUrl,
            'submitted_date' => $this->submitted_date ? $this->submitted_date->toIso8601String() : null,
            'approved_date' => $this->approved_date ? $this->approved_date->toIso8601String() : null,
        ];
    }
}
