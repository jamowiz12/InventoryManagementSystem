<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            throw new RuntimeException('Inventory schema migration requires PostgreSQL. Run the provided SQL scripts on Supabase PostgreSQL.');
        }
        if (Schema::hasTable('categories') && Schema::hasTable('stock_transactions')) {
            return;
        }

        $root = dirname(__DIR__, 3).'/database';
        foreach (['01_create_tables.sql', '02_create_functions.sql', '03_create_procedures.sql', '04_insert_sample_data.sql'] as $file) {
            DB::unprepared(file_get_contents($root.'/'.$file));
        }
    }

    public function down(): void
    {
        DB::unprepared('DROP TABLE IF EXISTS stock_transactions, products, suppliers, categories CASCADE');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_stock_value(BIGINT), fn_get_stock_status(BIGINT)');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_add_product(BIGINT, BIGINT, VARCHAR, TEXT, NUMERIC, INTEGER, INTEGER, BIGINT)');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_stock_in(BIGINT, INTEGER, TEXT), sp_stock_out(BIGINT, INTEGER, TEXT)');
    }
};
