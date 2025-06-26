<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesLogsTable extends Migration
{
    public function up()
    {
        Schema::create('services_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('service_id');
            $table->integer('user_type')->comment('(1=admin, 2=main_distributors, 3=distributors, 4=retailers, 5=customer)');
            $table->double('purchase_rate', 10, 2)->default(0);
            $table->double('sale_rate', 10, 2)->default(0);
            $table->bigInteger('main_distributor_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->double('main_distributor_commission', 10, 2)->default(0);
            $table->double('distributor_commission', 10, 2)->default(0);
            $table->tinyInteger('status')->comment('(0=expire, 1=active)');
            $table->dateTime('assign_date');
            $table->dateTime('decline_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services_logs');
    }
}
