<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'complete_name' => $this->full_name,
            'first_name' => $this->firstname,
            'middle_name' => $this->middlefirstname,
            'last_name' => $this->lastname,
            'second_last_name' => $this->middlelastname,
            'email' => $this->email,
            'identification' => $this->identification,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'document_type_id' => $this->document_type_id,
            'document_type' => $this->document_type->name,
            'roles' => $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'status' => $this->status->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
