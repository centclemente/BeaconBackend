<?php

namespace App\Http\Controllers;


use App\Services\UserService;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\UserResource;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\DisplayRequest;


class UserController extends Controller
{
    use ApiResponse;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(DisplayRequest $request)
    {

        $users = $this->userService->getAll($request->all());

        if ($users->isEmpty()) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Users']));
        }

        return $this->responseSuccess(message: __('messages.display', ['module' => 'Users']), data: $users);
    }

    public function update(UpdateRequest $request, $id)
    {

        $updatedUser = $this->userService->update($id, $request->all());

        if (!$updatedUser) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'User']));
        }

        return $this->responseSuccess(__('messages.updated', ['module' => 'User']), new UserResource($updatedUser));

    }

    public function destroy($id)
    {

       if (auth()->id() == $id) {
            return $this->responseUnprocessable("You can't archive your own account.");
        }
        $archivedUser = $this->userService->toggleArchived($id);

        if (!$archivedUser) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'User']));
        }

     
        $message = $archivedUser->trashed()
            ? __('messages.archived', ['module' => 'User'])
            : __('messages.restored', ['module' => 'User']);


        return $this->responseSuccess($message, new UserResource($archivedUser));
    }
}
