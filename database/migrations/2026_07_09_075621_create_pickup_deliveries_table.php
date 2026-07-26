<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pickup_deliveries', function (Blueprint $table) {

        $table->id();

        $table->foreignId('order_id')->constrained()->cascadeOnDelete();

        $table->string('address');

        $table->enum('status',[
            'Menunggu Pickup',
            'Dalam Perjalanan',
            'Sudah Dijemput',
            'Sedang Diantar',
            'Selesai'
        ]);

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_deliveries');
    }
};
