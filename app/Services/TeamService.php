<?php

namespace App\Services;

use App\Models\Team;
use App\Http\Resources\TeamResource;
use Essa\APIToolKit\Api\ApiResponse;


class TeamService
{
    use ApiResponse;
    public function getAll($data)
    {
        $status = $data['status'] ?? 'active';
        $pagination = $data['pagination'] ?? null;

        $team = Team::when($status == 'inactive', function ($query) {
            $query->onlyTrashed();
        })

            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            TeamResource::collection($team);
        } else {
            $team = TeamResource::collection($team);
        }

        return $team;
    }

    public function getById(int $id)
    {

    }

    public function create(array $data)
    {
        $team = [];

        foreach ($data["teams"] as $teamData) {
            if (
                Team::where("name", $teamData["name"])->exists() ||
                Team::where("code", $teamData["code"])->exists()
            ) {
                return $this->responseUnprocessable(
                    "Department with the same name or code already exists"
                );
            }

            $teamItem = Team::create([
                "name" => $teamData["name"],
                "code" => $teamData["code"],
            ]);

            $team[] = $teamItem;
        }

        return $team;
    }

    public function update(int $id, array $data)
    {

        $team = Team::withTrashed()->find($id);

        if (!$team) {
            return null;
        }

        $team->update($data);

        if (isset($data['add_systems'])) {
            $team->systems()->syncWithoutDetaching($data['add_systems']);
        }

       
        if (isset($data['remove_systems'])) {
            $team->systems()->detach($data['remove_systems']);
        }

        if (isset($data['system_ids'])) {
            $team->systems()->sync($data['system_ids']);
        }

        $team->load('systems');
        return $team;
    }

    public function toggleArchived(int $id)
    {
        $team = Team::withTrashed()->find($id);

        $trashed = $team->trashed();

        if ($trashed) {
            $team->restore();
        } else {
            $team->delete();
        }
        return $team;
    }
}