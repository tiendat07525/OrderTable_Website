<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tables')->whereIn('location', ['Sáº£nh chÃ­nh', 'Sảnh Chính'])->update([
            'location' => 'Sảnh chính',
        ]);

        DB::table('tables')->where('location', 'SÃ¢n thÆ°á»£ng')->update([
            'location' => 'Sân thượng',
        ]);
    }

    public function down(): void
    {
        DB::table('tables')->where('location', 'Sảnh chính')->update([
            'location' => 'Sáº£nh chÃ­nh',
        ]);

        DB::table('tables')->where('location', 'Sân thượng')->update([
            'location' => 'SÃ¢n thÆ°á»£ng',
        ]);
    }
};
