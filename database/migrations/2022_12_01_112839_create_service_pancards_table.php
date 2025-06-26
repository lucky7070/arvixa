<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePancardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_pancards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('user_id');
            $table->integer('user_type')->comment('( 2=main_distributors, 3=distributors, 4=retailers)');
            $table->tinyInteger('type')->default(1);
            $table->string('name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone', 20);
            $table->string('gender', 20);
            $table->text('doc');
            $table->text('nsdl_formdata')->nullable();
            $table->string('nsdl_txn_id')->nullable();
            $table->enum('is_physical_card', ['Y', 'N'])->default('Y');
            $table->tinyInteger('is_refunded')->default(0);
            $table->text('error_message')->nullable();
            $table->string('nsdl_ack_no')->nullable();
            $table->tinyInteger('nsdl_complete')->default(0);
            $table->timestamps();
            $table->timestamp('created_at_gmt')->useCurrent();
            $table->timestamp('updated_at_gmt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_pancards');
    }
}
