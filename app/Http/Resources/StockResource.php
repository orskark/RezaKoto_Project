<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'name' => optional($this->product_variant)->sku . ' (' . optional($this->product_variant->product)->name . ')',
            'product_variant_id' => $this->product_variant_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'reserved_quantity' => $this->reserved_quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'product_variant' => optional($this->product_variant)->sku . ' (' . optional($this->product_variant->product)->name . ')',
            'warehouse' => optional($this->warehouse)->name,
            'status' => optional($this->status)->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
