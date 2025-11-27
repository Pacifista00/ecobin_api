<?php

namespace App\Http\Controllers;

use App\Helpers\FcmSend;
use App\Models\Bin;
use App\Models\History;
use App\Models\Notification;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function bins()
    {
        $bins = Bin::with('sensor')->get();

        return response()->json([
            'message' => 'Bin data viewed successfully',
            'bins' => $bins,
        ], 200);
    }
    public function store(Request $request, $token)
    {
        $deviceToken = config('services.firebase.device_token');

        $request->validate([
            'organic_volume' => 'required|integer|min:1|max:100',
            'anorganic_volume' => 'required|integer|min:1|max:100',
        ]);

        $bin = Bin::where('token', $token)->firstOrFail();

        $sensor = Sensor::create([
            'organic_volume' => $request->organic_volume,
            'anorganic_volume' => $request->anorganic_volume,
            'bin_id' => $bin->id,
        ]);

        // 🔹 Cek status lama
        $organicWasFull = $bin->organic_full;
        $anorganicWasFull = $bin->anorganic_full;

        // 🔹 Update status penuh di tabel bin
        if ($request->organic_volume > 85) {
            $bin->update([
                'organic_full' => true
            ]);
        }
        if ($request->anorganic_volume > 85) {
            $bin->update([
                'anorganic_full' => true
            ]);
        }

        if (!$organicWasFull && $request->organic_volume > 85) {
            Notification::create([
                'title' => 'Tempat Sampah Organik Penuh',
                'description' => 'Kapasitas ' . $bin->name . ' organik sudah lebih dari 85%. Segera kosongkan untuk menghindari sampah menumpuk!',
            ]);
            FcmSend::send(
                $deviceToken,
                "Ecobin",
                "Kapasitas {$bin->name} organik sudah lebih dari 85%. Segera kosongkan untuk menghindari sampah menumpuk!"
                ,
                ["page" => "home"]
            );
        }

        if (!$anorganicWasFull && $request->anorganic_volume > 85) {
            Notification::create([
                'title' => 'Tempat Sampah Anorganik Penuh',
                'description' => 'Kapasitas ' . $bin->name . ' anorganik sudah lebih dari 85%. Segera kosongkan untuk menghindari sampah menumpuk!',
            ]);
            FcmSend::send(
                $deviceToken,
                "Ecobin",
                "Kapasitas {$bin->name} anorganik sudah lebih dari 85%. Segera kosongkan untuk menghindari sampah menumpuk!"
                ,
                ["page" => "home"]
            );
        }

        // Organik dikosongkan
        if (
            $request->organic_volume < 15 &&
            $organicWasFull // cek status lama, bukan $bin setelah update
        ) {
            History::create([
                'bin_id' => $bin->id,
                'information' => $bin->name . ' organik dikosongkan pada ' . now()->format('H:i'),
            ]);

            // Reset status penuh
            $bin->update(['organic_full' => false]);
        }

        // Anorganik dikosongkan
        if (
            $request->anorganic_volume < 15 &&
            $anorganicWasFull
        ) {
            History::create([
                'bin_id' => $bin->id,
                'information' => $bin->name . 'anorganik dikosongkan pada ' . now()->format('d-m-Y H:i'),
            ]);

            // Reset status penuh
            $bin->update(['anorganic_full' => false]);
        }


        return response()->json([
            'message' => 'Sensor data saved successfully',
            'sensor' => $sensor,
            'bin' => $bin,
        ], 201);
    }
}
