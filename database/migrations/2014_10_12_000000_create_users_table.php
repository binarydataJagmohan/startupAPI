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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('country_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            // $table->string('email_verification_token')->nullable();
            $table->string('password')->nullable();
            $table->string('new_password')->nullable();
            $table->string('gender')->nullable();;
            $table->string('city')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->text('profile')->nullable();
            $table->string('investorType')->nullable();
            // $table->string('experience')->nullable();
            $table->string('profile_pic')->nullable();
            $table->text('profile_desc')->nullable();
            // $table->boolean('kyc_purposes')->nullable();
            $table->enum('status',['active','inactive','deleted'])->default('active');
            // $table->enum('role',['user','investor','founder'])->default('user');
            $table->string('role');
            $table->enum('approval_status',['approved','pending','cencelled'])->default('pending');
            $table->boolean('reg_step_1')->default(0);
            $table->boolean('reg_step_2')->default(0);
            $table->boolean('reg_step_3')->default(0);
            $table->boolean('reg_step_4')->default(0);
            $table->boolean('is_profile_completed')->default(0);
            $table->boolean('is_email_verification_complete')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
