<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index('sale_lines_foreign');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('qty');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->bigInteger('discount_value')->nullable();
            $table->decimal('discount_amount', 12)->default(0);
            $table->decimal('tax_amount', 12)->default(0);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('unit_price', 10)->default(0);
            $table->decimal('net_price', 12);
            $table->decimal('sub_total', 10)->default(0);
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
        Schema::dropIfExists('sale_lines');
    }
}
