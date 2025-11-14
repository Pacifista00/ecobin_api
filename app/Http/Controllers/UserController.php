<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all([
            'id',
            'fullname',
            'username',
            'email',
            'role',
            'created_at'
        ]);

        return response()->json([
            'message' => 'Daftar pengguna berhasil diambil.',
            'users' => $users,
        ], 200);
    }
    public function storeUser(Request $request)
    {
        $authUser = $request->user();

        if (!in_array($authUser->role, ['super-admin', 'admin'])) {
            return response()->json([
                'message' => 'Akses Ditolak. Anda tidak memiliki izin untuk menambahkan pengguna baru.'
            ], 403);
        }

        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            // Username harus unik di tabel users
            'username' => 'required|string|max:255|unique:users,username',
            // Email harus unik dan format email valid
            'email' => 'required|string|email|max:255|unique:users,email',
            // Password harus minimal 8 karakter dan harus ada konfirmasi field (password_confirmation)
            'password' => 'required|string|min:8|confirmed',
            // Role harus salah satu dari nilai yang diizinkan
            'role' => 'required|string|in:super-admin,admin,cleaning-service,user',
        ]);

        // 3. Buat Pengguna Baru
        $user = User::create([
            'fullname' => $validatedData['fullname'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            // Hash password sebelum disimpan
            'password' => Hash::make($validatedData['password']),
        ]);

        // 4. Kembalikan Respons Sukses
        return response()->json([
            'message' => 'Pengguna baru berhasil ditambahkan.',
            // Kembalikan data yang dibuat (kecuali password)
            'user' => $user->only(['id', 'fullname', 'username', 'email', 'role', 'created_at']),
        ], 201); // Status Code 201 menandakan resource berhasil dibuat
    }
    public function updateUser(Request $request, int $id)
    {
        $user = User::find($id);
        $authUser = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        $rules = [
            'fullname' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'sometimes|string|in:super-admin,admin,cleaning-service,user',
        ];

        $validatedData = $request->validate($rules);

        $dataToUpdate = [];

        if (isset($validatedData['fullname'])) {
            $dataToUpdate['fullname'] = $validatedData['fullname'];
        }
        if (isset($validatedData['username'])) {
            $dataToUpdate['username'] = $validatedData['username'];
        }
        if (isset($validatedData['email'])) {
            $dataToUpdate['email'] = $validatedData['email'];
        }

        if (isset($validatedData['password'])) {
            if (!empty($validatedData['password'])) {
                $dataToUpdate['password'] = Hash::make($validatedData['password']);
            }
        }

        if (isset($validatedData['role'])) {
            if ($authUser && $authUser->role === 'super-admin') {
                $dataToUpdate['role'] = $validatedData['role'];
            } else {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk mengubah peran (role) pengguna.'
                ], 403);
            }
        }

        if (!empty($dataToUpdate)) {
            $user->update($dataToUpdate);
        }

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'user' => $user->only(['id', 'fullname', 'username', 'email', 'role'])
        ], 200);
    }

    public function deleteUser(Request $request, int $id)
    {
        $authUser = $request->user();

        if (!in_array($authUser->role, ['super-admin', 'admin'])) {
            return response()->json([
                'message' => 'Akses Ditolak. Anda tidak memiliki izin untuk menghapus pengguna.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        if ($authUser->id === $user->id) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun Anda sendiri melalui endpoint ini.'
            ], 403);
        }

        if ($user->role === 'super-admin' && $authUser->role !== 'super-admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus akun Super-Admin.'
            ], 403);
        }


        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.'
        ], 200);
    }
}
