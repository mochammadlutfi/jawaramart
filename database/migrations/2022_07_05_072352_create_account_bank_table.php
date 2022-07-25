<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_bank', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 191);
            $table->char('code', 6);
            $table->string('account_name', 191);
            $table->string('account_no', 191);
            $table->string('logo', 191)->nullable();
            $table->string('QR');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('account_bank');
    }
}
