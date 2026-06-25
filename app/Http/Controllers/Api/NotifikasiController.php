<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Get user notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('dibuat_pada', 'desc')
            ->get();

        $belumDibaca = $notifikasi->where('sudah_dibaca', false)->count();

        return response()->json([
            'success' => true,
            'data' => $notifikasi,
            'unread_count' => $belumDibaca
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notifikasi = Notifikasi::where('user_id', $user->id)->find($id);

        if (!$notifikasi) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notifikasi->sudah_dibaca = true;
        $notifikasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca'
        ]);
    }
}
