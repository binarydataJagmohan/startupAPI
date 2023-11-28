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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->text('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_image')->nullable();
            $table->string('tag')->nullable();
            $table->longtext('description')->nullable();
            $table->longtext('image')->nullable();
            $table->text('meta_tag')->nullable();
            $table->text('meta_desc')->nullable();
            $table->enum('status', ['active', 'deactive', 'delete'])->default('active');
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
        Schema::dropIfExists('blogs');
    }
};
