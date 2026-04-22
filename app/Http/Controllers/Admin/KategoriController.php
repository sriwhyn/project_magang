<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $kategori = Kategori::orderBy('tipe')->orderBy('nama_kategori')->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($kategori);
        }

        return view('admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe'          => 'required|in:kehilangan,kerusakan,semua',
        ]);

        $kategori = Kategori::create($request->only('nama_kategori', 'tipe'));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Kategori berhasil ditambahkan', 'data' => $kategori], 201);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe'          => 'required|in:kehilangan,kerusakan,semua',
        ]);

        $kategori->update($request->only('nama_kategori', 'tipe'));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Kategori berhasil diupdate', 'data' => $kategori]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        try {
            $kategori->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Kategori berhasil dihapus']);
            }

            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Kategori tidak dapat dihapus karena sedang digunakan.'], 400);
            }
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sedang digunakan.');
        }
    }
}
