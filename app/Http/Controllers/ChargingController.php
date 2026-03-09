<?php

namespace App\Http\Controllers;

use App\Models\Charging;
use App\Services\ChargingService;
use Illuminate\Http\Request;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\ChargingRequest;
use App\Http\Resources\ChargingResource;
use App\Http\Requests\ChargingUpdateRequest;


class ChargingController extends Controller
{
    use ApiResponse;

    protected ChargingService $chargingService;
    public function __construct(ChargingService $chargingService)
    {
        $this->chargingService = $chargingService;
    }
    public function index(DisplayRequest $request)
    {
        $charging = $this->chargingService->getAll($request->all());    

       
        

        return $this->responseSuccess('Charging display successfully', ChargingResource::collection($charging));
    }


    public function store(ChargingRequest $request)
    {
        $validated = $request->validated();

        $departments = [];

        foreach ($validated["departments"] as $dept) {
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

        return $this->responseSuccess('Departments created successfully', $departments);
    }

    public function update(ChargingUpdateRequest $request, $id)
    {
        $charging = Charging::withTrashed()->find($id);
        $validated = $request->validated();

        if (!$charging) {
            return $this->responseNotFound(["Department not found"]);
        }

        $charging->update($validated);

        return $this->responseSuccess(
            "Department updated successfully",
            new ChargingResource($charging)
        );
    }

    public function destroy($id)
    {
        $charging = Charging::withTrashed()->find($id);
        if (!$charging) {
            return $this->responseNotFound(["Department not found"]);
        }

        if ($charging->trashed()) {
            $charging->restore();

            return $this->responseSuccess(
                "Department restored successfully",
                new ChargingResource($charging)
            );
        }
        if ($charging->users()->exists()) {
            return $this->responseUnprocessable(
                "Cannot delete department with associated users"
            );
        }

        $charging->delete();
        return $this->responseSuccess(
            "Department deleted successfully",
            new ChargingResource($charging)
        );

    }
}
