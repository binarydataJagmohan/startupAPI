<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('investor_bookings', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id')->nullable();
        $table->integer('business_id')->nullable();
        $table->integer('business_unit_id')->nullable();
        $table->integer('subscription_value')->nullable();
        $table->timestamp('repayment_date')->nullable();
        $table->integer('repayment_value')->nullable();
        $table->integer('no_of_units')->nullable();
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
        Schema::dropIfExists('investor_bookings');
    }
};
