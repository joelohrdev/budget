<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if due_day column exists (production database)
        if (Schema::hasColumn('bills', 'due_day')) {
            // Add the new due_date column as nullable first
            Schema::table('bills', function (Blueprint $table) {
                $table->date('due_date')->nullable()->after('amount');
            });

            // Convert existing due_day values to due_date
            // Get all bills and update them individually
            $bills = DB::table('bills')->whereNull('due_date')->get();

            foreach ($bills as $bill) {
                $currentDate = now();
                $year = $currentDate->year;
                $month = $currentDate->month;

                // If the due day has already passed this month, use next month
                if ($bill->due_day < $currentDate->day) {
                    $month++;
                    if ($month > 12) {
                        $month = 1;
                        $year++;
                    }
                }

                // Handle months with fewer days
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $day = min($bill->due_day, $daysInMonth);

                $dueDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

                DB::table('bills')
                    ->where('id', $bill->id)
                    ->update(['due_date' => $dueDate]);
            }

            // Make due_date not nullable
            Schema::table('bills', function (Blueprint $table) {
                $table->date('due_date')->nullable(false)->change();
            });

            // Drop the old index and column
            Schema::table('bills', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'due_day']);
                $table->dropColumn('due_day');
            });

            // Add the new index
            Schema::table('bills', function (Blueprint $table) {
                $table->index(['user_id', 'due_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bills', 'due_date')) {
            // Add back due_day column
            Schema::table('bills', function (Blueprint $table) {
                $table->integer('due_day')->nullable()->after('amount');
            });

            // Convert due_date back to due_day
            DB::statement('UPDATE bills SET due_day = DAY(due_date) WHERE due_day IS NULL');

            // Make due_day not nullable
            Schema::table('bills', function (Blueprint $table) {
                $table->integer('due_day')->nullable(false)->change();
            });

            // Drop the new index and column
            Schema::table('bills', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'due_date']);
                $table->dropColumn('due_date');
            });

            // Add back the old index
            Schema::table('bills', function (Blueprint $table) {
                $table->index(['user_id', 'due_day']);
            });
        }
    }
};
