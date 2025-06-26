<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Retailer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retailers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('userId')->nullable();
            $table->string('name');
            $table->uuid('slug')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('status')->default('1');
            $table->bigInteger('main_distributor_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('device_id')->nullable();
            $table->string('fcm_id')->nullable();
            $table->string('registor_from')->default(1)->comment('1 : Admin, 2 : Front Web');
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
        Schema::dropIfExists('retailers');
    }
}
