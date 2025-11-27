<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class BinController extends Controller
{
    // Get all bins
    public function index()
    {
        $bins = Bin::all();

        // Ubah path file jadi URL
        $bins->map(function ($bin) {
            if ($bin->location_photo) {
                $bin->location_photo_url = asset('storage/' . $bin->location_photo);
            }
            return $bin;
        });

        return response()->json($bins);
    }

    // Create new bin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'location_description' => 'required|string',
        ]);

        // Simpan file foto
        $path = $request->file('location_photo')->store('bins', 'public');

        // Generate token 10 digit
        do {
            $token = Str::random(10);
        } while (Bin::where('token', $token)->exists());

        $bin = Bin::create([
            'name' => $request->name,
            'location_photo' => $path,
            'location_description' => $request->location_description,
            'token' => $token,
        ]);

        return response()->json([
            'message' => 'Bin created successfully',
            'bin' => $bin,
            'photo_url' => asset('storage/' . $bin->location_photo),
        ], 201);
    }

    // Show single bin
    public function show($id)
    {
        $bin = Bin::findOrFail($id);
        $bin->location_photo_url = asset('storage/' . $bin->location_photo);

        return response()->json($bin);
    }

    // Update bin
    public function update(Request $request, $id)
    {
        $bin = Bin::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location_photo' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'location_description' => 'sometimes|string',
        ]);

        $data = $request->only(['name', 'location_description']);

        // Kalau ada file baru, hapus lama & simpan baru
        if ($request->hasFile('location_photo')) {
            if ($bin->location_photo && Storage::disk('public')->exists($bin->location_photo)) {
                Storage::disk('public')->delete($bin->location_photo);
            }
            $path = $request->file('location_photo')->store('bins', 'public');
            $data['location_photo'] = $path;
        }

        $bin->update($data);

        return response()->json([
            'message' => 'Bin updated successfully',
            'bin' => $bin,
            'photo_url' => asset('storage/' . $bin->location_photo),
        ]);
    }

    // Delete bin
    public function destroy($id)
    {
        $bin = Bin::findOrFail($id);

        // Hapus file foto juga
        if ($bin->location_photo && Storage::disk('public')->exists($bin->location_photo)) {
            Storage::disk('public')->delete($bin->location_photo);
        }

        $bin->delete();

        return response()->json(['message' => 'Bin deleted successfully']);
    }
}
