<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_register', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('admin_id');
            $table->decimal('opened_amount', 12);
            $table->decimal('closed_amount', 12)->nullable()->default(0);
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
        Schema::dropIfExists('cash_register');
    }
}
