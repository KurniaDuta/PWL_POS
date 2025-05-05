<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;

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
            'harga_jual' => 'required|numeric|min:0|gt:harga_beli'
        ]);

        $barang = BarangModel::create($request->all());

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
            'harga_jual' => 'sometimes|numeric|min:0'
        ]);

        $barang->update($request->all());

        return response()->json(BarangModel::with('kategori')->find($barang->barang_id));
    }

    public function destroy(BarangModel $barang) 
    {
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil dihapus'
        ]);
    }
}
