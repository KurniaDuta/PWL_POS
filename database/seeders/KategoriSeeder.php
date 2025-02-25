<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kategori_kode' => 'MKN', 'kategori_nama' => 'Makanan', 'created_at' => now()],
            ['kategori_kode' => 'MNM', 'kategori_nama' => 'Minuman', 'created_at' => now()],
            ['kategori_kode' => 'FSH', 'kategori_nama' => 'Fashion', 'created_at' => now()],
            ['kategori_kode' => 'ELK', 'kategori_nama' => 'Elektronik', 'created_at' => now()],
            ['kategori_kode' => 'KSH', 'kategori_nama' => 'Kesehatan', 'created_at' => now()],
        ];

        DB::table('m_kategori')->insert($data);
    }
}
