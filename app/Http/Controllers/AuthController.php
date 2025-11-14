<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
            'role' => 'in:super-admin,admin,cleaning-service,user'
        ]);

        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user'
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'user' => $user]);
    }
    public function loggedUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return response()->json([
            'id' => $user->id,
            'fullname' => $user->fullname,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
    public function updateProfile(Request $request) // Hapus $id dari parameter
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $authUser;

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role' => 'nullable|in:super-admin,admin,cleaning-service,user'
        ]);

        $data = [
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
        ];

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->only(['id', 'name', 'email', 'username', 'role'])
        ], 200);
    }
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // 3. Periksa apakah kata sandi lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini salah.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Kata sandi berhasil diubah.'
        ], 200);
    }
}
