<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Services\TeamService;
use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\TeamUpdateRequest;


class TeamController extends Controller
{
    use ApiResponse;
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(DisplayRequest $request)
    {
        $teams = $this->teamService->getAll($request->all());


        if ($teams->isEmpty()) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Teams']));
        }

        return $this->responseSuccess(__('messages.display', ['module' => 'Teams']), $teams);
    }

    public function store(TeamRequest $request)
    {
        $validated = $request->validated();

        $team = $this->teamService->create($validated);

        return $this->responseSuccess(__('messages.created', ['module' => 'Teams']), TeamResource::collection($team));
    }



    public function update(TeamUpdateRequest $request, $id)
    {

        $team = $this->teamService->update($id, $request->all());

        if (!$team) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Team']));
        }

        return $this->responseSuccess(__('messages.updated', ['module' => 'Team']), new TeamResource($team));

    }


    public function destroy($id)
    {
        $teams=$this->teamService->toggleArchived($id);

        if (!$teams) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Team']));
        }

        $message = $teams->trashed()
            ? __('messages.archived', ['module' => 'Team'])
            : __('messages.restored', ['module' => 'Team']);

        return $this->responseSuccess($message, new TeamResource($teams));

                


    }
    }
