<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanDetailModel;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index() 
    {
        $details = PenjualanDetailModel::with(['penjualan', 'barang.kategori'])->get();
        return response()->json($details);
    }

    public function store(Request $request) 
    {   
        $request->validate([
            'penjualan_id' => 'required|exists:t_penjualan,penjualan_id',
            'barang_id' => 'required|exists:m_barang,barang_id',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1'
        ]);

        $detail = PenjualanDetailModel::create([
            'penjualan_id' => $request->penjualan_id,
            'barang_id' => $request->barang_id,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah
        ]);

        return response()->json($detail->load(['penjualan', 'barang']), 201);
    }

    public function show(PenjualanDetailModel $penjualanDetail) 
    {
        $detail = $penjualanDetail->load(['penjualan', 'barang.kategori']);
        
        $response = $detail->toArray();
        
        if (isset($response['barang']) && isset($response['barang']['image'])) {
            $response['barang']['image_url'] = url('storage/posts/' . $response['barang']['image']);
        }
        
        return response()->json($response);
    }

    public function update(Request $request, PenjualanDetailModel $penjualanDetail) 
    {   
        $request->validate([
            'penjualan_id' => 'sometimes|exists:t_penjualan,penjualan_id',
            'barang_id' => 'sometimes|exists:m_barang,barang_id',
            'harga' => 'sometimes|numeric|min:0',
            'jumlah' => 'sometimes|integer|min:1'
        ]);

        $penjualanDetail->update($request->all());

        $updatedDetail = PenjualanDetailModel::with(['penjualan', 'barang.kategori'])
            ->find($penjualanDetail->detail_id);
        
        // Add image URL
        $response = $updatedDetail->toArray();
        if (isset($response['barang']) && isset($response['barang']['image'])) {
            $response['barang']['image_url'] = url('storage/posts/' . $response['barang']['image']);
        }

        return response()->json($response);
    }

    public function destroy(PenjualanDetailModel $penjualanDetail) 
    {
        $penjualanDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data detail penjualan berhasil dihapus'
        ]);
    }

    public function byPenjualan($penjualan_id)
    {
        $details = PenjualanDetailModel::with(['barang.kategori'])
            ->where('penjualan_id', $penjualan_id)
            ->get();
        
        $response = $details->toArray();
        
        foreach ($response as &$detail) {
            if (isset($detail['barang']) && isset($detail['barang']['image'])) {
                $detail['barang']['image_url'] = url('storage/posts/' . $detail['barang']['image']);
            }
        }
        
        return response()->json($response);
    }
}
