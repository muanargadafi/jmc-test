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
        Schema::create('incoming_goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->date('received_date');
            $table->string('origin', 200);
            $table->string('letter_number', 100)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->decimal('total_price', 15, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('operator_id');
            $table->index('sub_category_id');
            $table->index('received_date');

            $table->foreign('operator_id')
                ->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('sub_category_id')
                ->references('id')->on('sub_categories')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreign('verified_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('incoming_goods');
    }
};
