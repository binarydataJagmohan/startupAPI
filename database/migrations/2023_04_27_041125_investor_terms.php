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
        Schema::create('investor_terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('principal_residence',['0','1'])->default('0');
            $table->enum('cofounder',['0','1'])->default('0');
            $table->enum('prev_investment_exp',['0','1'])->default('0');
            $table->enum('experience',['0','1'])->default('0');
            $table->enum('net_worth',['0','1'])->default('0');
            $table->enum('no_requirements',['0','1'])->default('0');
            $table->enum('annual_income',['0','1'])->default('0');
            $table->enum('financial_net_worth',['0','1'])->default('0');
            $table->enum('financial_annual_net_worth',['0','1'])->default('0');
            $table->enum('foreign_annual_income',['0','1'])->default('0');
            $table->enum('foreign_net_worth',['0','1'])->default('0');
            $table->enum('foreign_annual_net_worth',['0','1'])->default('0');
            $table->enum('corporate_net_worth',['0','1'])->default('0');
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investor_terms');
    }
};
