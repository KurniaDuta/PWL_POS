<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar Penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select(
            'penjualan_id',
            'user_id',
            'pembeli',
            'penjualan_kode',
            'penjualan_tanggal'
        )->with(['user', 'details', 'details.barang']);

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return $penjualan->details->sum('jumlah');
            })
            ->addColumn('total_harga', function ($penjualan) {
                return $penjualan->details->sum(function ($detail) {
                    return $detail->harga * $detail->jumlah;
                });
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm mr-1">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm mr-1">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect('/');
        }

        try {
            $penjualan = PenjualanModel::with('details')->findOrFail($id);
            $penjualan->details()->delete();
            $penjualan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data penjualan'
            ]);
        }
    }

    public function create_ajax()
    {
        $barangs = BarangModel::select('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
            ->join('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
            ->groupBy('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
            ->havingRaw('SUM(t_stok.stok_jumlah) > 0')
            ->get();

        return view('penjualan.create_ajax', compact('barangs'));
    }

    public function store_ajax(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect('/');
        }

        try {
            $rules = [
                'pembeli' => 'required|string|max:255',
                'details' => 'required|array|min:1',
                'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
                'details.*.jumlah' => 'required|integer|min:1',
                'details.*.harga' => 'required|numeric|min:0'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422);
            }


            $penjualan = PenjualanModel::create([
                'user_id' => auth()->id(),
                'pembeli' => $request->pembeli,
                'penjualan_kode' => 'PJ' . date('YmdHis'),
                'penjualan_tanggal' => now()
            ]);

            foreach ($request->details as $detail) {
                $penjualan->details()->create([
                    'barang_id' => $detail['barang_id'],
                    'harga' => $detail['harga'],
                    'jumlah' => $detail['jumlah']
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::with(['user', 'details', 'details.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Pembeli');
        $sheet->setCellValue('D1', 'Kasir');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Total Item');
        $sheet->setCellValue('G1', 'Total Harga');

        $sheet->getStyle("A1:G1")->getFont()->setBold(true);

        $baris = 2;
        $no = 1;

        foreach ($penjualan as $value) {
            $totalItem = $value->details->sum('jumlah');
            $totalHarga = $value->details->sum(function ($detail) {
                return $detail->harga * $detail->jumlah;
            });

            $sheet->setCellValue("A" . $baris, $no);
            $sheet->setCellValue("B" . $baris, $value->penjualan_kode);
            $sheet->setCellValue("C" . $baris, $value->pembeli);
            $sheet->setCellValue("D" . $baris, $value->user->nama);
            $sheet->setCellValue("E" . $baris, date('d-m-Y', strtotime($value->penjualan_tanggal)));
            $sheet->setCellValue("F" . $baris, $totalItem);
            $sheet->setCellValue("G" . $baris, $totalHarga);

            $sheet->getStyle("A{$baris}:G{$baris}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8E8E8');

            $baris++;

            $sheet->setCellValue("B" . $baris, "Detail Barang:");
            $sheet->setCellValue("C" . $baris, "Jumlah");
            $sheet->setCellValue("D" . $baris, "Harga");
            $sheet->setCellValue("E" . $baris, "Subtotal");
            $sheet->getStyle("B{$baris}:E{$baris}")->getFont()->setBold(true);

            $baris++;

            foreach ($value->details as $detail) {
                $sheet->setCellValue("B" . $baris, $detail->barang->barang_nama);
                $sheet->setCellValue("C" . $baris, $detail->jumlah);
                $sheet->setCellValue("D" . $baris, $detail->harga);
                $sheet->setCellValue("E" . $baris, $detail->jumlah * $detail->harga);

                $sheet->getStyle("D{$baris}:E{$baris}")->getNumberFormat()
                    ->setFormatCode('#,##0');

                $baris++;
            }

            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->getStyle("A1:G" . ($baris - 1))->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setTitle("Data Penjualan");

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date("Y-m-d H-i-s") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::with(['user', 'details', 'details.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function edit_ajax(string $id)
    {
        try {
            $penjualan = PenjualanModel::with(['user', 'details', 'details.barang'])->findOrFail($id);
            $barangs = BarangModel::select('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
                ->join('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
                ->groupBy('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
                ->havingRaw('SUM(t_stok.stok_jumlah) > 0')
                ->get();

            return view('penjualan.edit_ajax', compact('penjualan', 'barangs'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }
    }

    public function update_ajax(Request $request, string $id)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect('/');
        }

        try {
            $rules = [
                'pembeli' => 'required|string|max:255',
                'details' => 'required|array|min:1',
                'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
                'details.*.jumlah' => 'required|integer|min:1',
                'details.*.harga' => 'required|numeric|min:0'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $penjualan = PenjualanModel::findOrFail($id);

            // Update penjualan header
            $penjualan->update([
                'pembeli' => $request->pembeli,
                'user_id' => auth()->id()
            ]);

            // Delete existing details
            $penjualan->details()->delete();

            // Create new details
            foreach ($request->details as $detail) {
                $penjualan->details()->create([
                    'barang_id' => $detail['barang_id'],
                    'harga' => $detail['harga'],
                    'jumlah' => $detail['jumlah']
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data penjualan'
            ], 500);
        }
    }
}
