<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('slug', 191);
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('category_id')->index('product_category_foreign');
            $table->tinyInteger('has_variant')->default(0);
            $table->string('var1_name', 191)->nullable();
            $table->string('var2_name', 191)->nullable();
            $table->text('var1_value')->nullable();
            $table->text('var2_value')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable()->index('product_brand_foreign');
            $table->integer('berat')->nullable();
            $table->string('berat_satuan', 155)->nullable();
            $table->float('lebar', 10, 0)->nullable();
            $table->float('panjang', 10, 0)->nullable();
            $table->float('tinggi', 10, 0)->nullable();
            $table->boolean('show_web')->default(false);
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
        Schema::dropIfExists('product');
    }
}
