<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\DisplaySystemRequest;
use App\Http\Requests\SystemRequest;
use App\Http\Resources\SystemResource;
use App\Models\Systems;
use Essa\APIToolKit\Api\ApiResponse;

class SystemsController extends Controller
{
    use ApiResponse;
    public function index(DisplaySystemRequest $request)
    {
        $status = $request->input('status');
        $scope = $request->input('scope');
        $teamId = $request->input('team_id');
        $pagination = $request->input('pagination', true);



        $systems = Systems::with(['progress.category', 'progress.updatedBy', 'team'])
            ->when($status == 'inactive', function ($query) {
                $query->onlyTrashed();
            })
            ->when($scope == 'per_team', function ($query) use ($teamId) {
                $query->whereHas('team', function ($q) use ($teamId) {
                    $q->where('teams.id', $teamId);
                });
            })
            ->useFilters()
            ->dynamicPaginate();

        if (SystemResource::collection($systems)->isEmpty()) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Systems']));
        }

        if (!$pagination) {
            return SystemResource::collection($systems);
        } else {
            $systems = SystemResource::collection($systems);
        }

        return $this->responseSuccess(__('messages.display', ['module' => 'Systems']), $systems);
    }

    public function store(SystemRequest $request)
    {
        $validated = $request->validated();

        $system = Systems::create([
            'name' => $validated['system_name'],
        ]);

        $system->team()->attach($validated['team_id']);
        $system->load('team');

        return $this->responseSuccess(__('messages.created', ['module' => 'System']), new SystemResource($system));
    }





}