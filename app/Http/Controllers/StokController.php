<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\StokModel;
use App\Models\SupplierModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar Stok yang terdaftar dalam sistem'
        ];

        $activeMenu = 'stok';

        $kategori = KategoriModel::all();

        return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user']);

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $stok->whereHas('barang', function ($query) use ($request) {
                $query->where('kategori_id', $request->kategori_id);
            });
        }

        return DataTables::of($stok)
            ->addIndexColumn()

            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm mr-1">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm mr-1">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';

                return $btn;
            })

            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function edit_ajax(string $id)
    {
        try {
            $stok = StokModel::with(['barang', 'supplier', 'user'])->findOrFail($id);
            $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
            $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();

            return view('stok.edit_ajax', compact('stok', 'kategori', 'suppliers'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
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
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:0'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $stok = StokModel::findOrFail($id);

            $stok->update([
                'barang_id' => $request->barang_id,
                'supplier_id' => $request->supplier_id,
                'stok_tanggal' => $request->stok_tanggal,
                'stok_jumlah' => $request->stok_jumlah,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data stok'
            ], 500);
        }
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect('/');
        }

        try {
            $stok = StokModel::findOrFail($id);
            $stok->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data stok'
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.show_ajax', ['stok' => $stok]);
    }

    public function create_ajax()
    {
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('stok.create_ajax', compact('barangs', 'suppliers', 'kategori'));
    }

    public function store_ajax(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect('/');
        }

        try {
            $rules = [
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            StokModel::create([
                'barang_id' => $request->barang_id,
                'supplier_id' => $request->supplier_id,
                'stok_tanggal' => $request->stok_tanggal,
                'stok_jumlah' => $request->stok_jumlah,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data stok'
            ], 500);
        }
    }

    public function getBarang($kategori_id)
    {
        try {
            $barang = BarangModel::where('kategori_id', $kategori_id)
                ->select('barang_id', 'barang_nama')
                ->get();
                
            return response()->json([
                'status' => true,
                'barang' => $barang
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data barang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'stok_id' => $value['A'],
                            'barang_id' => $value['B'],
                            'supplier_id' => $value['C'],
                            'stok_jumlah' => $value['D'],
                            'user_id' => auth()->id(),
                            'stok_tanggal' => now(),
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    StokModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('supplier_id')
            ->with('supplier')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Stok ID');
        $sheet->setCellValue('C1', 'Barang');
        $sheet->setCellValue('D1', 'Supplier');
        $sheet->setCellValue('E1', 'Pengisi');
        $sheet->setCellValue('F1', 'Tanggal');
        $sheet->setCellValue('G1', 'Jumlah Stok');

        $sheet->getStyle("A1:G1")->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($stok as $key => $value) {
            $sheet->setCellValue("A" . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->stok_id);
            $sheet->setCellValue("C" . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->supplier->supplier_nama);
            $sheet->setCellValue("E" . $baris, $value->user->nama);
            $sheet->setCellValue('F' . $baris, date('d-m-Y', strtotime($value->stok_tanggal)));
            $sheet->setCellValue("G" . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data Stok");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date("Y-m-d H:i:s") . '.xlsx';

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Cache-Control: max-age=0");
        header("Cache-Control: max-age=1");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate('D, d M Y H:i:s') . ' GMT');
        header("Cache-Control: cache, must-revalidate");
        header("Pragma: public");

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('supplier_id')
            ->with('supplier')
            ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Barang ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
