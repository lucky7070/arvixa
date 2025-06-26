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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('designation_id');
            $table->string('name');
            $table->uuid('slug')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('status')->default('1');
            $table->string('image')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('employees');
    }
};
