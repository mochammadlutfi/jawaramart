<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->enum('type', ['offline', 'online', 'transfer manual', 'e-wallet']);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('image')->nullable();
            $table->boolean('pos_ok');
            $table->boolean('web_ok');
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
        Schema::dropIfExists('account_payment_methods');
    }
}
