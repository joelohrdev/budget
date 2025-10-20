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
        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'due_day']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('due_day');
            $table->date('due_date')->after('amount');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'due_date']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->integer('due_day')->after('amount');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->index(['user_id', 'due_day']);
        });
    }
};
