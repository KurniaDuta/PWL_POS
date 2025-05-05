<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index() 
    {
        return BarangModel::with('kategori')->get();
    }

    public function store(Request $request) 
    {   
        $request->validate([
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0|gt:harga_beli',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Create barang with image
        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'image' => $image->hashName()
        ]);

        return response()->json($barang, 201);
    }

    public function show(BarangModel $barang) 
    {
        return response()->json($barang->load('kategori'));
    }

    public function update(Request $request, BarangModel $barang) 
    {   
        $request->validate([
            'kategori_id' => 'sometimes|exists:m_kategori,kategori_id',
            'barang_kode' => 'sometimes|string|unique:m_barang,barang_kode,'.$barang->barang_id.',barang_id',
            'barang_nama' => 'sometimes|string',
            'harga_beli' => 'sometimes|numeric|min:0',
            'harga_jual' => 'sometimes|numeric|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'

        ]);
        
        $data = [
            'kategori_id' => $request->kategori_id ?? $barang->kategori_id,
            'barang_kode' => $request->barang_kode ?? $barang->barang_kode,
            'barang_nama' => $request->barang_nama ?? $barang->barang_nama,
            'harga_beli' => $request->harga_beli ?? $barang->harga_beli,
            'harga_jual' => $request->harga_jual ?? $barang->harga_jual,
        ];

        if ($request->hasFile('image')) {
            if ($barang->image && Storage::exists('public/posts/'.$barang->image)) {
                Storage::delete('public/posts/'.$barang->image);
            }
            
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            $data['image'] = $image->hashName();
        }
        
        $barang->update($data);

        return response()->json(BarangModel::with('kategori')->find($barang->barang_id));
    }

    public function destroy(BarangModel $barang) 
    {
        if ($barang->image && Storage::exists('public/posts/'.$barang->image)) {
            Storage::delete('public/posts/'.$barang->image);
        }
        
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil dihapus'
        ]);
    }
}
