<?php

namespace App\Http\Controllers;

use App\Helpers\FcmSend;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->get();

        return response()->json([
            'message' => 'Semua notifikasi berhasil diambil.',
            'notifications' => $notifications
        ], 200);
    }
    public function test()
    {
        $deviceToken = config('services.firebase.device_token');

        $result = FcmSend::send(
            $deviceToken,
            "Halooooo!",
            "Ini notifikasi dari Laravel menggunakan FCM v1",
            ["page" => "home"]
        );

        return response()->json($result);
    }
}
