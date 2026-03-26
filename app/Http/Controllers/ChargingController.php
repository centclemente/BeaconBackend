<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChargingRequest;
use App\Http\Requests\ChargingUpdateRequest;
use App\Http\Requests\DisplayRequest;
use App\Http\Resources\ChargingResource;
use App\Models\Charging;
use App\Services\ChargingService;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ChargingController extends Controller
{
    use ApiResponse;

    // protected ChargingService $chargingService;
    // public function __construct(ChargingService $chargingService)
    // {
    //     $this->chargingService = $chargingService;
    // }
    // public function index(DisplayRequest $request)
    // {
    //     $charging = $this->chargingService->getAll($request->all());    




    //     return $this->responseSuccess('Charging display successfully', ChargingResource::collection($charging));
    // }


    // public function store(ChargingRequest $request)
    // {
    //     $validated = $request->validated();

    //     $departments = [];

    //     foreach ($validated["departments"] as $dept) {
    //         if (
    //             Charging::where("name", $dept["name"])->exists() ||
    //             Charging::where("code", $dept["code"])->exists()
    //         ) {
    //             return $this->responseUnprocessable(
    //                 "Department with the same name or code already exists"
    //             );
    //         }

    //         $department = Charging::create([
    //             "name" => $dept["name"],
    //             "code" => $dept["code"],
    //         ]);

    //         $departments[] = new ChargingResource($department);
    //     }

    //     return $this->responseSuccess('Departments created successfully', $departments);
    // }

    // public function update(ChargingUpdateRequest $request, $id)
    // {
    //     $charging = Charging::withTrashed()->find($id);
    //     $validated = $request->validated();

    //     if (!$charging) {
    //         return $this->responseNotFound(["Department not found"]);
    //     }

    //     $charging->update($validated);

    //     return $this->responseSuccess(
    //         "Department updated successfully",
    //         new ChargingResource($charging)
    //     );
    // }

    // public function destroy($id)
    // {
    //     $charging = Charging::withTrashed()->find($id);
    //     if (!$charging) {
    //         return $this->responseNotFound(["Department not found"]);
    //     }

    //     if ($charging->trashed()) {
    //         $charging->restore();

    //         return $this->responseSuccess(
    //             "Department restored successfully",
    //             new ChargingResource($charging)
    //         );
    //     }
    //     if ($charging->users()->exists()) {
    //         return $this->responseUnprocessable(
    //             "Cannot delete department with associated users"
    //         );
    //     }

    //     $charging->delete();
    //     return $this->responseSuccess(
    //         "Department deleted successfully",
    //         new ChargingResource($charging)
    //     );

    public function sync_charging()
    {
        $url = "https://api-one.rdfmis.com/api/charging_api?pagination=none";


        $response = Http::withHeaders([
            "API_KEY" => env("CHARGING_API_KEY"),
        ])->get($url);

        if ($response->failed()) {
            return response()->json(
                ["message" => "Failed to fetch Charging"],
                500
            );
        }

        $data = $response->json("data");

        $sync = collect($data)
            ->map(function ($charging) {
                return [
                    "sync_id" => $charging["id"],
                    "code" => $charging["code"],
                    "name" => $charging["name"],
                    "company_id" => $charging["company_id"],
                    "company_code" => $charging["company_code"],
                    "company_name" => $charging["company_name"],
                    "business_unit_id" => $charging["business_unit_id"],
                    "business_unit_code" => $charging["business_unit_code"],
                    "business_unit_name" => $charging["business_unit_name"],
                    "department_id" => $charging["department_id"],
                    "department_code" => $charging["department_code"],
                    "department_name" => $charging["department_name"],
                    "unit_id" => $charging["unit_id"],
                    "unit_code" => $charging["unit_code"],
                    "unit_name" => $charging["unit_name"],
                    "sub_unit_id" => $charging["sub_unit_id"],
                    "sub_unit_code" => $charging["sub_unit_code"],
                    "sub_unit_name" => $charging["sub_unit_name"],
                    "location_id" => $charging["location_id"],
                    "location_code" => $charging["location_code"],
                    "location_name" => $charging["location_name"],
                    "deleted_at" => !empty($charging["deleted_at"])
                        ? date("Y-m-d", strtotime($charging["deleted_at"]))
                        : null,
                ];
            })
            ->toArray();



        Charging::upsert($sync, ["sync_id"], [
            "code",
            "name",
            "company_id",
            "company_code",
            "company_name",
            "business_unit_id",
            "business_unit_code",
            "business_unit_name",
            "department_id",
            "department_code",
            "department_name",
            "unit_id",
            "unit_code",
            "unit_name",
            "sub_unit_id",
            "sub_unit_code",
            "sub_unit_name",
            "location_id",
            "location_code",
            "location_name",
            "deleted_at"
        ]);

        return $this->responseSuccess('Charging synced successfully', ChargingResource::collection(Charging::whereIn('sync_id', collect($sync)->pluck('sync_id'))->get()));
    }





}

