<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('user_id');
            $table->text('address');
            $table->string('name', 191);
            $table->string('reciever', 191);
            $table->string('phone', 20);
            $table->char('area_id', 13);
            $table->string('area_text')->nullable();
            $table->char('postal_code', 8);
            $table->boolean('is_primary')->default(false);
            $table->string('lat', 30)->nullable();
            $table->string('lng', 30)->nullable();
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
        Schema::dropIfExists('user_address');
    }
}
