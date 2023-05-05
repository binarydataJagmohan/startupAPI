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
       Schema::create('business_units', function (Blueprint $table) {
    $table->id();
    $table->integer('business_id');
    $table->integer('avg_amt_per_person')->nullable();
    $table->integer('minimum_subscription')->nullable();
    $table->timestamp('closed_in')->nullable();
    $table->integer('total_units');
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
        Schema::dropIfExists('business_units');
    }
};
