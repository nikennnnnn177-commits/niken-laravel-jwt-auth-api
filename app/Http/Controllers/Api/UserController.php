<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        return response()->json(ApiFormatter::createJson(
            200, 'Users retrieved', User::all()
        ), 200);
    }

    public function createUser(Request $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(ApiFormatter::createJson(
            201, 'User created', $user
        ), 201);
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json(ApiFormatter::createJson(
                404, 'User not found'
            ), 404);

        return response()->json(ApiFormatter::createJson(
            200, 'User retrieved', $user
        ), 200);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json(ApiFormatter::createJson(
                404, 'User not found'
            ), 404);

        $user->update($request->only('name','email'));

        return response()->json(ApiFormatter::createJson(
            200, 'User updated', $user
        ), 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json(ApiFormatter::createJson(
                404, 'User not found'
            ), 404);

        $user->delete();

        return response()->json(ApiFormatter::createJson(
            200, 'User deleted'
        ), 200);
    }
}
