<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePassRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $user = User::where("username", $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "username" => ["The provided credentials are incorrect."],
                "password" => ["The provided credentials are incorrect."],
            ]);
        }
        $token = $user->createToken("PersonalAccessToken")->plainTextToken;
        $user["token"] = $token;

        $cookie = cookie("auth_token", $token);
        $user->load("role", "team", "charging");

         return $this->responseSuccess(
            "Login Succesful",
            [
                "user" => new UserResource($user),
                'access_token' => $token,   
            ]
        )->withCookie($cookie);


        return $this->responseSuccess(
            "Login Succesful",
            [
                "user" => new UserResource($user),
                'access_token' => $token,   
            ]
        )->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return $this->responseSuccess("Logged out successfully")->withCookie(
            cookie()->forget("auth_token")
        );
    }


    public function register(UserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            "first_name" => $validated["first_name"],
            "middle_name" => $validated["middle_name"],
            "last_name" => $validated["last_name"],
            "suffix" => $validated["suffix"],
            "username" => $validated["username"],
            "password" => bcrypt($validated["password"]?? $validated["username"]),
            "role_id" => $validated["role_id"]?? 2,
            "charging_id" => $validated["charging_id"],
            "team_id" => $validated["team_id"],
        ]);

        $token = $user->createToken("auth_token")->plainTextToken;

        $user->load("role",'team','charging');

        return $this->responseCreated(
            "User created successfully",
            ["user" => new UserResource($user), "access_token" => $token]
        );  
    }

    public function ChangePassword(ChangePassRequest $request)
    {   
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                "current_password" => ["The current password is incorrect."],
            ]);
        }elseif ($request->current_password === $request->new_password) {
            throw ValidationException::withMessages([
                "new_password" => ["The new password must be different from the current password."],
            ]);
        }

           $user->update(["password" => Hash::make($request->new_password),]);
      

        return $this->responseSuccess("Password changed successfully");
    }

public function ResetPassword($id)
{
    $user = User::find($id);

    if (!$user) {
        return $this->responseNotFound("User not found");
    }

    $user->update([
        "password" => Hash::make($user->username),
    ]);

    $user->tokens()->delete();

    return $this->responseSuccess(
        "Password reset successfully to its default value"
    );
}

}
