<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'notes' => $this->notes,
            'gender' => $this->gender->name,
            'brand' => $this->brand->name,
            'category' => $this->category->name,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'gender_id' => $this->gender_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
