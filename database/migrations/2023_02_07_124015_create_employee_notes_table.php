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
        Schema::create('employee_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('retailer_id');
            $table->bigInteger('employee_id')->comment('Employee who wrote message.');
            $table->text('message');
            $table->dateTime('date');
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
        Schema::dropIfExists('employee_notes');
    }
};
