<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        $penjualanIds = DB::table('t_penjualan')->pluck('penjualan_id')->toArray();

        foreach ($penjualanIds as $penjualanId) {
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'penjualan_id' => $penjualanId,
                    'barang_id' => rand(1, 15),
                    'harga' => rand(10000, 2000000),
                    'jumlah' => rand(1, 10),
                    'created_at' => now(),
                ];
            }
        }

        DB::table('t_penjualan_detail')->insert($data);
    }
}
