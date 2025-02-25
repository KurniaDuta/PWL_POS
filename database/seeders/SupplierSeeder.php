<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['supplier_kode' => 'SP1', 'supplier_nama' => 'Supplier 1', 'supplier_alamat' => 'Jl. Supplier 1', 'created_at' => now()],
            ['supplier_kode' => 'SP2', 'supplier_nama' => 'Supplier 2', 'supplier_alamat' => 'Jl. Supplier 2', 'created_at' => now()],
            ['supplier_kode' => 'SP3', 'supplier_nama' => 'Supplier 3', 'supplier_alamat' => 'Jl. Supplier 3', 'created_at' => now()],
        ];

        DB::table('m_supplier')->insert($data);
    }
}
