<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('product_variant_foreign');
            $table->string('variant', 191)->nullable();
            $table->string('variant2', 191)->nullable();
            $table->string('sku', 191)->nullable();
            $table->decimal('sell_price', 12);
            $table->decimal('purchase_price', 12);
            $table->bigInteger('qty')->default(0);
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
        Schema::dropIfExists('product_variant');
    }
}
