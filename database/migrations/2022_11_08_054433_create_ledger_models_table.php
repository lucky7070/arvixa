<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgerModelsTable extends Migration
{
    public function up()
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('voucher_no');
            $table->bigInteger('user_id');
            $table->integer('user_type')->comment('( 2=main_distributors, 3=distributors, 4=retailers)');
            $table->double('amount', 10, 2)->comment("Trans Amount.");
            $table->double('current_balance', 10, 2);
            $table->double('updated_balance', 10, 2);
            $table->integer('payment_type')->comment('(1=Credit, 2= Debit)');
            $table->integer('payment_method')->comment('1 : Payment Request, \n 2 : Transfer to Bank, \n 3 : Online Payment,\n 4 : Withdrawal Request,\n 5 : Service Charge  ');
            $table->text('trans_details_json')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('request_id')->nullable()->comment('if payment_method \n 1 - Payment Request \n 4 - Withdrawal Request, \n 5 - Service Charge \n Other null ');
            $table->text('particulars')->nullable();
            $table->dateTime('date')->useCurrent();
            $table->integer('paid_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledgers');
    }
}
