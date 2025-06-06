<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'unit_price'            => $this->unit_price,
            'subtotal'              => $this->subtotal,
            'product_snapshot_json' => $this->product_snapshot_json,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
