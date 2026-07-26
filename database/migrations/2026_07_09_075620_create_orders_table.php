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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->date('order_date');

        $table->decimal('weight',5,2);

        $table->enum('service_type',[
            'Cuci Kering',
            'Cuci Setrika',
            'Setrika'
        ]);

        $table->enum('pickup_method',[
            'Pickup',
            'Datang Langsung'
        ]);

        $table->enum('status',[
            'Menunggu',
            'Diproses',
            'Dicuci',
            'Dikeringkan',
            'Disetrika',
            'Selesai',
            'Diantar'
        ])->default('Menunggu');

        $table->decimal('total_price',10,2)->default(0);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
