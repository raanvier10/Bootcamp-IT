<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikel = Artikel::with('penulis')
            ->where('sudah_diterbitkan', true)
            ->orderBy('diterbitkan_pada', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Artikel Edukasi',
            'data' => $artikel
        ], 200);
    }
}
