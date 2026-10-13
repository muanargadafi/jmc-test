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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->after('id');
            $table->string('username', 100)->unique()->after('name');
            $table->boolean('is_locked')->default(false)->after('remember_token');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_locked');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            $table->foreign('role_id')
                ->references('id')->on('roles')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'role_id',
                'username',
                'is_locked',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
