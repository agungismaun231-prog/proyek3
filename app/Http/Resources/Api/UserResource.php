<?php

namespace App\Http\Resources\Api;

use App\Models\Customer;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $phone = '';
        $address = '';
        $city = '';
        $postalCode = '';
        $specialization = '';
        $status = '';

        if ($this->role === 'customer') {
            $customer = $this->relationLoaded('customer')
                ? $this->customer
                : Customer::where('email', $this->email)->first();

            if ($customer) {
                $phone = $customer->phone ?? '';
                $address = $customer->address ?? '';
                $city = $customer->city ?? '';
                $postalCode = $customer->postal_code ?? '';
            }
        } elseif ($this->role === 'technician') {
            $technician = $this->relationLoaded('technician')
                ? $this->technician
                : (Technician::where('user_id', $this->id)->first() ?? Technician::where('email', $this->email)->first());

            if ($technician) {
                $phone = $technician->phone ?? '';
                $address = $technician->address ?? '';
                $specialization = $technician->specialization ?? '';
                $status = $technician->status ?? '';
            }
        }

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'role' => (string) ($this->role ?? 'customer'),
            'phone' => (string) $phone,
            'address' => (string) $address,
            'city' => (string) $city,
            'postal_code' => (string) $postalCode,
            'specialization' => (string) $specialization,
            'status' => (string) $status,
        ];
    }
}
