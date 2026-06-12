<?php

namespace Database\Seeders;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            //SẢNH CHÍNH (4 bàn)
            ['name' => 'Bàn S1', 'capacity' => 2, 'location' => 'Sảnh chính', 'status' => 'available', 'price' => 2000],
            ['name' => 'Bàn S2', 'capacity' => 4, 'location' => 'Sảnh chính', 'status' => 'available', 'price' => 3000],
            ['name' => 'Bàn S3', 'capacity' => 6, 'location' => 'Sảnh chính', 'status' => 'available', 'price' => 2500],
            ['name' => 'Bàn S4', 'capacity' => 8, 'location' => 'Sảnh chính', 'status' => 'available', 'price' => 35000],

            //SÂN THƯỢNG (3 bàn)
            ['name' => 'Bàn T1', 'capacity' => 2, 'location' => 'Sân thượng', 'status' => 'available', 'price' => 5000],
            ['name' => 'Bàn T2', 'capacity' => 4, 'location' => 'Sân thượng', 'status' => 'available', 'price' => 7000],
            ['name' => 'Bàn T3', 'capacity' => 6, 'location' => 'Sân thượng', 'status' => 'available', 'price' => 10000],

            //KHU VIP (3 bàn)
            ['name' => 'Bàn VIP1', 'capacity' => 6, 'location' => 'Khu VIP', 'status' => 'available', 'price' => 15000],
            ['name' => 'Bàn VIP2', 'capacity' => 8, 'location' => 'Khu VIP', 'status' => 'available', 'price' => 2000],
            ['name' => 'Bàn VIP3', 'capacity' => 10, 'location' => 'Khu VIP', 'status' => 'available', 'price' => 3000]
        ]);
    }
}