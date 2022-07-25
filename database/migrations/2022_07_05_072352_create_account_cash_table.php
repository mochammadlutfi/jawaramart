<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_cash', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor', 50);
            $table->string('ref', 191)->nullable();
            $table->dateTime('tgl');
            $table->unsignedBigInteger('payment_method_id');
            $table->string('note')->nullable();
            $table->enum('type', ['inbound', 'outbound', 'transfer']);
            $table->decimal('total', 12);
            $table->unsignedBigInteger('staff_id');
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
        Schema::dropIfExists('account_cash');
    }
}
