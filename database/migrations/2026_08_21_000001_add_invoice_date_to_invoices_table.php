<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invoices', 'invoice_date')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->date('invoice_date')->nullable()->after('invoice_number');
            });
        }

        $duplicateCount = DB::table('invoices')
            ->select('invoice_number')
            ->groupBy('invoice_number')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $hasUnique = collect(Schema::getIndexes('invoices'))
            ->contains(function ($index) {
                return ($index['unique'] ?? false)
                    && ($index['columns'] ?? []) === ['invoice_number'];
            });

        if ($duplicateCount === 0 && ! $hasUnique) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unique('invoice_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'invoice_date')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('invoice_date');
            });
        }
    }
};
