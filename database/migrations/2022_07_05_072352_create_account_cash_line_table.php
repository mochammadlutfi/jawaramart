<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCashLineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_cash_line', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('journal_id');
            $table->decimal('jumlah', 12);
            $table->unsignedBigInteger('cash_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_cash_line');
    }
}
