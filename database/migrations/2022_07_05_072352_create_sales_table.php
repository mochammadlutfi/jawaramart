<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->unsignedBigInteger('customer_id');
            $table->boolean('is_pos')->default(false);
            $table->boolean('is_web')->default(false);
            $table->string('ref', 191)->nullable();
            $table->decimal('shipping_cost', 12)->nullable()->default(0);
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->string('discount_type', 191)->nullable();
            $table->decimal('discount_amount', 12)->nullable()->default(0);
            $table->integer('discount_value')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'partial', 'pending']);
            $table->dateTime('payment_due')->nullable();
            $table->enum('status', ['draft', 'pending', 'done', 'cancel', 'delivery', 'confirmed', 'recieved']);
            $table->decimal('total', 12)->default(0);
            $table->decimal('grand_total', 12);
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 12)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
