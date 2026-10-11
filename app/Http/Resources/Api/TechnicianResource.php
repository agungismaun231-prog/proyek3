<?php

namespace App\Http\Resources\Api;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TechnicianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avgRating = $this->ratings_avg_rating ?? ($this->relationLoaded('ratings') ? $this->ratings->avg('rating') : Rating::averageForTechnician($this->id));
        $ratingCount = $this->ratings_count ?? ($this->relationLoaded('ratings') ? $this->ratings->count() : Rating::countForTechnician($this->id));

        $photoUrl = '';
        if (!empty($this->photo)) {
            $photoUrl = str_starts_with($this->photo, 'http')
                ? $this->photo
                : url(Storage::url($this->photo));
        }

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'phone' => (string) ($this->phone ?? ''),
            'address' => (string) ($this->address ?? ''),
            'specialization' => (string) ($this->specialization ?? ''),
            'status' => (string) ($this->status ?? 'available'),
            'average_rating' => round((float) $avgRating, 2),
            'rating_count' => (int) $ratingCount,
            'description' => (string) ($this->description ?? ''),
            'years_experience' => (int) ($this->years_experience ?? 0),
            'photo_url' => (string) $photoUrl,
        ];
    }
}
