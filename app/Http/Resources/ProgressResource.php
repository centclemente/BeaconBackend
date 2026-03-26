<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
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
            'id' => $this->id,
            'description' => $this->description,
            'raised_date' => $this->raised_date,
            'target_date' => $this->target_date,
            'end_date' => $this->end_date,
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'created at' => $this->created_at,
            'updated at' => $this->updated_at,
            'deleted at' => $this->deleted_at,
            
        ];
    }
}
