<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSaleLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_lines', function (Blueprint $table) {
            $table->foreign(['sale_id'], 'sale_lines_foreign')->references(['id'])->on('sales')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_lines', function (Blueprint $table) {
            $table->dropForeign('sale_lines_foreign');
        });
    }
}
