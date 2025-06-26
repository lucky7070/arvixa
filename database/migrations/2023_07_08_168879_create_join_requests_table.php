<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJoinRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('join_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('request_for');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('join_requests');
    }
}
