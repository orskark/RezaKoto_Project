<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'enterprise_id' => $this->enterprise_id,
            'brand_id' => $this->brand_id,
            'gender_id' => $this->gender_id,
            'category_id' => $this->category_id,
            'enterprise' => $this->enterprise->name,
            'brand' => $this->brand->name,
            'gender' => $this->gender->name,
            'category' => $this->category->name,
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->value,
            'status' => $this->status->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
