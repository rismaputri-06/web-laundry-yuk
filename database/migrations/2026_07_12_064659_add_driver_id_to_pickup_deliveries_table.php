<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pickup_deliveries', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('order_id')->constrained('drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pickup_deliveries', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });
    }
};