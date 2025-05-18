<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
        'id'          => $this->id,
        'name'        => $this->name,
        'description' => $this->description,
        'NIT'         => $this->NIT,
        'phone_number'=> $this->phone_number,
        'address'     => $this->address,
        'email'       => $this->email,
        ];    
    }
}
