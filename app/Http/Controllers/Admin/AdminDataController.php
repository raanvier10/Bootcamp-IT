<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Kategori;
use App\Models\PesanKontak;

class AdminDataController extends Controller
{
    // === WILAYAH (DISTRICTS) ===
    public function districts()
    {
        $wilayahs = Wilayah::orderBy('nama', 'asc')->get();
        return view('admin.districts', compact('wilayahs'));
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:wilayah',
            'nama' => 'required|string|max:100',
        ]);

        Wilayah::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.districts')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function updateDistrict(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:wilayah,kode,' . $id,
            'nama' => 'required|string|max:100',
        ]);

        $wilayah = Wilayah::findOrFail($id);
        $wilayah->update([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.districts')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroyDistrict($id)
    {
        $wilayah = Wilayah::findOrFail($id);
        // Cek apakah wilayah ini sedang dipakai oleh laporan atau petugas
        if (\App\Models\Laporan::where('wilayah_id', $id)->exists() || \App\Models\User::where('wilayah_id', $id)->exists()) {
            return redirect()->route('admin.districts')->with('error', 'Tidak dapat menghapus wilayah karena masih digunakan oleh data laporan atau petugas.');
        }
        
        $wilayah->delete();
        return redirect()->route('admin.districts')->with('success', 'Wilayah berhasil dihapus.');
    }

    // === KATEGORI (CATEGORIES) ===
    public function categories()
    {
        $kategoris = Kategori::orderBy('nama', 'asc')->get();
        return view('admin.categories', compact('kategoris'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $kategori = Kategori::findOrFail($id);
        if (\App\Models\Laporan::where('kategori_id', $id)->exists()) {
            return redirect()->route('admin.categories')->with('error', 'Tidak dapat menghapus kategori karena masih digunakan oleh laporan.');
        }
        
        $kategori->delete();
        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dihapus.');
    }

    // === PESAN KONTAK (CONTACT MESSAGES) ===
    public function messages()
    {
        $pesans = PesanKontak::orderBy('dibuat_pada', 'desc')->paginate(15);
        return view('admin.messages', compact('pesans'));
    }

    public function destroyMessage($id)
    {
        PesanKontak::findOrFail($id)->delete();
        return redirect()->route('admin.messages')->with('success', 'Pesan berhasil dihapus.');
    }
}
