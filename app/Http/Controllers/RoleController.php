<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Essa\APIToolKit\Api\ApiResponse;


class RoleController extends Controller
{
   use ApiResponse;
    public function index(DisplayRequest $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $role=Role::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($role->isEmpty()) {
            return $this->responseNotFound('No role found');
        }

        if (!$pagination) {
            RoleResource::collection($role);
        } else {
            $role = RoleResource::collection($role);
        }

        return $this->responseSuccess('Role display successfully', $role);
    }


    public function store(RoleRequest $request)
    {   
        $validated = $request->validated();

        $role = Role::create($validated);

        return $this->responseSuccess('Role created successfully', new RoleResource($role));
    }

    public function update(RoleRequest $request, $id)
    {
        $role = Role::withTrashed()->find($id);
        $validated = $request->validated();

        if (!$role) {
            return $this->responseNotFound('Role not found');
        }

        $role->update($validated);

        return $this->responseSuccess('Role updated successfully', new RoleResource($role));


    }

    public function destroy($id)
    {
        $role = Role::withTrashed()->find($id);

        if (!$role) {
            return $this->responseNotFound('Role not found');
        }

        if (!$role->trashed() && $role->users()->exists()) {
            return $this->responseUnprocessable('Cannot archive role with assigned users. Please reassign or remove users before archiving this role.');
        }

        if ($role->trashed()) {
            $role->restore();
            $message = 'Role restored successfully';
        } else {
            $role->delete();
            $message = 'Role archived successfully';
        }

        return $this->responseSuccess($message, new RoleResource($role));
    }

}
