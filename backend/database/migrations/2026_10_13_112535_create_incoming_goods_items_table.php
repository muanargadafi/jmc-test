<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incoming_goods_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incoming_good_id');
            $table->string('name', 200);
            $table->decimal('price', 15, 2);
            $table->decimal('quantity', 15, 2);
            $table->string('unit', 40);
            $table->decimal('total', 15, 2);
            $table->date('expired_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('incoming_good_id');

            $table->foreign('incoming_good_id')
                ->references('id')->on('incoming_goods')
                ->onDelete('cascade')
                ->onUpdate('restrict');
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_goods_items');
    }
};
