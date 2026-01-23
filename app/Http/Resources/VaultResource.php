<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'region' => $this->region,
            'worm_protection' => $this->worm_protection,
            'delete_protection' => $this->delete_protection,
            'kms_encryption' => $this->kms_encryption,
        ];
    }
}
