<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // -------------------------------------------------------------------------
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // -------------------------------------------------------------------------
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $user_validation = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|digits_between:8,14',
            'password' => 'required|min:6',
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('users_imgs', 'public');
        } else {
            $path = 'default.png';
        }

        $user = User::create([
            'name' => $user_validation['name'],
            'email' => $user_validation['email'],
            'phone_number' => $user_validation['phone_number'],
            'password' => Hash::make($user_validation['password']), // Hashing password
            'img' => $path,
            'admin' => false,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user_validation = $request->validate([
            'name' => 'nullable|string',
            'phone_number' => 'nullable|digits_between:8,14',
            'password' => 'nullable|min:6',
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);
        //----------------------------------------------------------
        if ($request->hasFile('img')) {
            if ($user->img !== 'default.png') {
                Storage::disk('public')->delete($user->img);
            }
            $path = $request->file('img')->store('users_imgs', 'public');
        } else {
            $path = $user->img; // If no new image is uploaded, keep the existing one
        }
        //----------------------------------------------------------
        $user->name = $user_validation['name'] ?? $user->name;
        $user->phone_number = $user_validation['phone_number'] ?? $user->phone_number;
        if (isset($user_validation['password'])) {
            $user->password = Hash::make($user_validation['password']);
        }
        $user->img = $path;
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
