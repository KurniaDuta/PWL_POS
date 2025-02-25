<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'user_id' => 3,
                'pembeli' => 'Pembeli ' . $i,
                'penjualan_kode' => 'PJ' . $i,
                'penjualan_tanggal' => now(),
                'created_at' => now(),
            ];
        }

        DB::table('t_penjualan')->insert($data);
    }
}
