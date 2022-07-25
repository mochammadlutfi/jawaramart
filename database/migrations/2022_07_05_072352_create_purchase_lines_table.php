<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('qty');
            $table->integer('qty_returned')->default(0);
            $table->integer('qty_sold')->default(0);
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_amount', 12)->nullable()->default(0);
            $table->integer('discount_value')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 12)->default(0);
            $table->decimal('unit_price', 12);
            $table->decimal('net_price', 12);
            $table->decimal('sub_total', 12);
            $table->date('exp_date')->nullable();
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
        Schema::dropIfExists('purchase_lines');
    }
}
