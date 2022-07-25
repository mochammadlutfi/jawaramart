<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ref', 191)->nullable();
            $table->unsignedBigInteger('sale_id');
            $table->dateTime('date');
            $table->string('discount_type', 191)->nullable();
            $table->enum('status', ['draft', 'confirmed', 'cancel']);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid']);
            $table->decimal('discount_amount', 11)->nullable();
            $table->integer('discount_value')->nullable();
            $table->decimal('total', 12);
            $table->decimal('grand_total', 12);
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
        Schema::dropIfExists('sale_return');
    }
}
