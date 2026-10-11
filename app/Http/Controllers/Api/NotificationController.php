<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Menampilkan semua notifikasi milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $notifications = Notification::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Menandai notifikasi tertentu sebagai sudah dibaca.
     */
    public function markAsRead(
        Request $request,
        Notification $notification
    )
    {
        // Cek apakah notifikasi ini milik user yang sedang login
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * Menghitung jumlah notifikasi yang belum dibaca (Langkah 15).
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => Notification::where(
                'user_id',
                $request->user()->id
            )
            ->where('is_read', false)
            ->count()
        ]);
    }
}