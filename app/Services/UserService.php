<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;
use Essa\APIToolKit\Api\ApiResponse;

class UserService
{
    use ApiResponse;
    public function getAll($data)
    {
        $status = $data['status'] ?? null;
        $pagination = $data['pagination'] ?? null;

        $users = User::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->with(["role", "charging",'team'])
            ->useFilters()
            ->dynamicPaginate(); 
        
        if (!$pagination) {
            UserResource::collection($users );
        } else {
            $users= UserResource::collection($users);
        }

        return $users;
    }
    public function update(int $id, array $data)
    {
    $user = User::find($id);
        if (!$user) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'User']));
        }

        $validated = $data;

        if (isset($validated["password"])) {
            $validated["password"] = bcrypt($validated["password"]);
        }

        $user->update($validated);
        $user->load("role", "charging");

        return $user;
    }

    public function toggleArchived(int $id)
    {

    $user = User::withTrashed()->find($id);

        $trashed = $user->trashed();

        if ($trashed) {
           $user->restore();
        } 
        else {
            $user->tokens()->delete();
            $user->delete();
        }   

        return $user;
        
    }
}