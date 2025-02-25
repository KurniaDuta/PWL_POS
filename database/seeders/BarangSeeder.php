<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'barang_kode' => 'MKN1',
                'barang_nama' => 'Nugget Frozen',
                'harga_beli'  => 45000,
                'harga_jual'  => 60000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'MKN2',
                'barang_nama' => 'Siomay Frozen',
                'harga_beli'  => 30000,
                'harga_jual'  => 40000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'MKN3',
                'barang_nama' => 'Sosis Daging',
                'harga_beli'  => 25000,
                'harga_jual'  => 35000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'MNM1',
                'barang_nama' => 'Teh Hijau',
                'harga_beli'  => 10000,
                'harga_jual'  => 15000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'MNM2',
                'barang_nama' => 'Sirup Melon',
                'harga_beli'  => 10000,
                'harga_jual'  => 20000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'MNM3',
                'barang_nama' => 'Jus Jeruk',
                'harga_beli'  => 10000,
                'harga_jual'  => 15000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'FSH1',
                'barang_nama' => 'Hoodie Marvel',
                'harga_beli'  => 150000,
                'harga_jual'  => 300000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'FSH2',
                'barang_nama' => 'T-Shirt DC',
                'harga_beli'  => 100000,
                'harga_jual'  => 200000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'FSH3',
                'barang_nama' => 'Sepatu Nike',
                'harga_beli'  => 80000,
                'harga_jual'  => 150000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 4,
                'barang_kode' => 'ELK1',
                'barang_nama' => 'Laptop Acer',
                'harga_beli'  => 500000,
                'harga_jual'  => 1000_000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 4,
                'barang_kode' => 'ELK2',
                'barang_nama' => 'Smartphone Samsung',
                'harga_beli'  => 400000,
                'harga_jual'  => 800000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 4,
                'barang_kode' => 'ELK3',
                'barang_nama' => 'Tablet Apple',
                'harga_beli'  => 300000,
                'harga_jual'  => 600000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 5,
                'barang_kode' => 'KSH1',
                'barang_nama' => 'Vitamin C',
                'harga_beli'  => 5000,
                'harga_jual'  => 10000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 5,
                'barang_kode' => 'KSH2',
                'barang_nama' => 'Paracetamol',
                'harga_beli'  => 10000,
                'harga_jual'  => 20000,
                'created_at'  => now(),
            ],
            [
                'kategori_id' => 5,
                'barang_kode' => 'KSH3',
                'barang_nama' => 'Antibiotik',
                'harga_beli'  => 15000,
                'harga_jual'  => 30000,
                'created_at'  => now(),
            ],
        ];

        DB::table('m_barang')->insert($data);
    }
}
