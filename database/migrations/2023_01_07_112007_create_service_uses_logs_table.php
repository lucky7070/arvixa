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
        Schema::create('service_uses_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_type')->comment('( 2=main_distributors, 3=distributors, 4=retailers)')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('service_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('request_id');
            $table->double('purchase_rate', 10, 2)->default(0);
            $table->double('sale_rate', 10, 2)->default(0);
            $table->bigInteger('main_distributor_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->double('main_distributor_commission', 10, 2)->default(0);
            $table->double('distributor_commission', 10, 2)->default(0);
            $table->integer('is_refunded')->default(0);
            $table->integer('used_in')->default(1)->comment('1 : Web Application, 2 : Api');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('created_at_gmt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_uses_logs');
    }
};
