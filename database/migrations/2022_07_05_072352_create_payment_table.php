<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('paymenttable_type', 191);
            $table->unsignedBigInteger('paymenttable_id')->nullable();
            $table->enum('type', ['inbound', 'outbound']);
            $table->decimal('amount', 12);
            $table->decimal('amount_received', 12);
            $table->unsignedBigInteger('payment_method_id')->index('payment_methods_foreign');
            $table->decimal('change', 12)->default(0);
            $table->text('note')->nullable();
            $table->string('ref', 191)->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('code')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
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
        Schema::dropIfExists('payment');
    }
}
