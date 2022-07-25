<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->foreign(['product_id'], 'cart_product_foreign')->references(['id'])->on('product')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'cart_user_foreign')->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['variant_id'], 'cart_product_variant_foreign')->references(['id'])->on('product_variant')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropForeign('cart_product_foreign');
            $table->dropForeign('cart_user_foreign');
            $table->dropForeign('cart_product_variant_foreign');
        });
    }
}
