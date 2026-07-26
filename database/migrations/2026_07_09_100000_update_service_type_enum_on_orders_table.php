<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY service_type ENUM('Cuci Lipat','Cuci Setrika','Setrika Saja') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY service_type ENUM('Cuci Kering','Cuci Setrika','Setrika') NOT NULL");
    }
};