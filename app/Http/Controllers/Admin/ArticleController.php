<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Artikel::latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'gambar_sampul' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published'
        ]);

        $data = $request->only(['judul', 'isi']);
        $data['penulis_id'] = auth()->id();
        $data['slug'] = Str::slug($request->judul);
        
        if ($request->status === 'published') {
            $data['sudah_diterbitkan'] = true;
            $data['diterbitkan_pada'] = now();
        } else {
            $data['sudah_diterbitkan'] = false;
            $data['diterbitkan_pada'] = null;
        }

        if ($request->hasFile('gambar_sampul')) {
            $path = $request->file('gambar_sampul')->store('artikel', 'public');
            $data['gambar_sampul'] = '/storage/' . $path;
        }

        Artikel::create($data);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $article = Artikel::findOrFail($id);
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Artikel::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'gambar_sampul' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published'
        ]);

        $data = $request->only(['judul', 'isi']);
        $data['slug'] = Str::slug($request->judul);

        if ($request->status === 'published' && !$article->sudah_diterbitkan) {
            $data['sudah_diterbitkan'] = true;
            $data['diterbitkan_pada'] = now();
        } elseif ($request->status === 'draft') {
            $data['sudah_diterbitkan'] = false;
            $data['diterbitkan_pada'] = null;
        } else {
            // Keep existing state for published status
            $data['sudah_diterbitkan'] = $article->sudah_diterbitkan;
            $data['diterbitkan_pada'] = $article->diterbitkan_pada;
        }

        if ($request->hasFile('gambar_sampul')) {
            $path = $request->file('gambar_sampul')->store('artikel', 'public');
            $data['gambar_sampul'] = '/storage/' . $path;
        }

        $article->update($data);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $article = Artikel::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
