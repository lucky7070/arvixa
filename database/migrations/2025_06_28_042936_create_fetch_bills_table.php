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
        Schema::create('fetch_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_id', 50);
            $table->integer('service_id');
            $table->unsignedInteger('user_id');
            $table->string('board_id', 100)->nullable();
            $table->string('consumer_no', 100)->nullable();
            $table->string('consumer_name', 100)->nullable();
            $table->string('bill_no', 50)->nullable();
            $table->decimal('bill_amount', 10, 0)->nullable();
            $table->date('due_date')->nullable();
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
        Schema::dropIfExists('fetch_bills');
    }
};
