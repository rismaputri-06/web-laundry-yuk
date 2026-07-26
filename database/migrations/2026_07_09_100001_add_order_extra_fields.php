<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_express')->default(false)->after('status');
            $table->text('notes')->nullable()->after('is_express');
        });

        Schema::table('pickup_deliveries', function (Blueprint $table) {
            $table->date('pickup_date')->nullable()->after('address');
            $table->string('pickup_time')->nullable()->after('pickup_date');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_express', 'notes']);
        });

        Schema::table('pickup_deliveries', function (Blueprint $table) {
            $table->dropColumn(['pickup_date', 'pickup_time']);
        });
    }
};