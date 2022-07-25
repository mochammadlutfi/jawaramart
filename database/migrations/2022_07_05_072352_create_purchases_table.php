<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ref', 191);
            $table->boolean('is_opening')->default(false);
            $table->unsignedBigInteger('supplier_id');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_amount', 12)->nullable();
            $table->enum('status', ['draft', 'pending', 'ordered', 'done', 'cancel'])->default('draft');
            $table->decimal('total', 12);
            $table->string('payment_status', 191)->nullable();
            $table->integer('discount_value')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 12)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('date');
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
        Schema::dropIfExists('purchases');
    }
}
