<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
return [
            'id'                    => $this->id,
            'quantity'              => $this->quantity,
            'reason'                => $this->reason,
            'product_variant' => optional($this->stock->product_variant)->sku . ' (' . optional($this->stock->product_variant->product)->name . ')',
            'movement_type' => optional($this->movement_type)->name,
            'status' => optional($this->status)->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
