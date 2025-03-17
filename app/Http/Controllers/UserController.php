<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $default = asset('storage/users_imgs/default.jpg');
        $user->img = $user->img ? asset('storage/' . $user->img) : asset('storage/default.png'); // Ensure default.png is accessible
        return response()->json(["user" => $user , "default_img" => $default]);
    }

    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $user_validation = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|digits_between:8,14',
            'password' => 'required|min:6',
            'img' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
            'address' => 'required|string|max:255',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('users_imgs', 'public');
        } else {
            $path = 'default.png';
        }

        $user = User::create([
            'name' => $user_validation['name'],
            'address' => $user_validation['address'],
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
        $user = User::find($id);
        if(!$user){
            return response()->json(["message" => 'user not found'] , 404) ;
        }
        try{
            $user_validation = $request->validate([
                'name' => 'required|string|min:3',
                'phone_number' => 'required|digits_between:8,14',
                'password' => 'nullable|min:6',
                'img' => 'nullable|mimes:png,jpg,jpeg|max:2048',
                'address' => 'required|string|max:255',    
            ]);

        
            //REMOVE_IMG IS AN ATTRIBUTE COMING FROM REACT TO SET THR USER IMG TO ITS DEFAULT STATE
            if ($request->hasFile('img') && $request->remove_img == "false") {
                if ($user->img && Storage::disk('public')->exists($user->img) && $user->img !== 'default.png') {
                    Storage::disk('public')->delete($user->img);
                }
                $path = $request->file('img')->store('users_imgs', 'public');
            } else {
                $request->remove_img ? $path = 'users_imgs/default.jpg' : $path = $user->img;
            }
            //----------------------------------------------------------
            $user->name = $user_validation['name'] ?? $user->name;
            $user->phone_number = $user_validation['phone_number'] ?? $user->phone_number;
            $user->address = $user_validation['address'] ?? $user->address;
            if (!empty($user_validation['password'])) {
                $user->password = Hash::make($user_validation['password']);
            }
            $user->img = $path;
            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch(ValidationException  $e){
            return response()->json(['errors' => $e->errors()], 422);
        }
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
