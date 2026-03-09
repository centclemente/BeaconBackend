<?php

namespace App\Services;

use App\Models\Charging;
use App\Http\Resources\ChargingResource;
use Essa\APIToolKit\Api\ApiResponse;

class ChargingService
{
    use ApiResponse;
    public function getAll($data)
    {
        $status = $data['status'] ?? null;
        $pagination = $data['pagination'] ?? null;

        $charging = Charging::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();


        if ($charging->isEmpty()) {
            return $this->responseNotFound('No charging found');
        }

        if (!$pagination) {
            ChargingResource::collection($charging);
        } else {
            $charging = ChargingResource::collection($charging);
        }

        return $charging;
    }

    public function create(array $data)
    {
        $departments = [];

        foreach ($data["departments"] as $dept) {
            if (
                Charging::where("name", $dept["name"])->exists() ||
                Charging::where("code", $dept["code"])->exists()
            ) {
                return $this->responseUnprocessable(
                    "Department with the same name or code already exists"
                );
            }

            $department = Charging::create([
                "name" => $dept["name"],
                "code" => $dept["code"],
            ]);

            $departments[] = new ChargingResource($department);
        }

        return $departments;

    }

    public function update(int $id, array $data)
    {
    }

    public function toggleArchived(int $id)
    {
    }
}