<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

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
}
