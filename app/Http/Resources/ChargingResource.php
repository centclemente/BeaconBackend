<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChargingResource extends JsonResource
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
            "name" => $this->name,
            "code" => $this->code,
            "company_id" => $this->company_id,
            "company_code" => $this->company_code,
            "company_name" => $this->company_name,
            "business_unit_id" => $this->business_unit_id,
            "business_unit_code" => $this->business_unit_code,
            "business_unit_name" => $this->business_unit_name,
            "department_id" => $this->department_id,
            "department_code" => $this->department_code,
            "department_name" => $this->department_name,
            "unit_id" => $this->unit_id,
            "unit_code" => $this->unit_code,
            "unit_name" => $this->unit_name,
            "sub_unit_id" => $this->sub_unit_id,
            "sub_unit_code" => $this->sub_unit_code,
            "sub_unit_name" => $this->sub_unit_name,
            "location_id" => $this->location_id,
            "location_code" => $this->location_code,
            "location_name" => $this->location_name,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
