<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "username" => $this->username,
            "role" => new RoleResource($this->whenLoaded('role')),
            "charging" => new ChargingResource($this->whenLoaded('charging')),
            'team' => new TeamResource($this->whenLoaded('team')),
            'deleted_at' => $this->deleted_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,

        ];
    }
}
