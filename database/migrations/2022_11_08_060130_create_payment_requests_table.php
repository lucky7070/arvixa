<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_number', 100);
            $table->bigInteger('user_id');
            $table->integer('user_type')->comment('( 2=main_distributors, 3=distributors, 4=retailers)');
            $table->double('amount', 10, 2)->default(0);
            $table->string('title');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->integer('status')->comment("0 : Pending, 1 : Approved, 2 : Rejected");
            $table->text('reason')->nullable()->comment('Comment on Approve / Reject.');
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
        Schema::dropIfExists('payment_requests');
    }
}
