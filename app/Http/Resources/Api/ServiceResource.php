<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'description' => (string) ($this->description ?? ''),
            'price' => (float) ($this->price ?? 0),
            'duration_minutes' => (int) ($this->duration_minutes ?? 0),
            'category' => (string) ($this->category ?? 'AC'),
        ];
    }
}
