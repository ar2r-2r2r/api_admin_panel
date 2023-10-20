<?php

namespace App\Http\Services;

use App\Events\EmployeeCreatedEvent;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'employee')->first()->id,
        ]);
        event(new EmployeeCreatedEvent($user->id, auth()->id()));

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ]);
    }

}
