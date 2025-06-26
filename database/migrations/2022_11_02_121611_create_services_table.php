<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('slug')->unique();
            $table->string('name');
            $table->text('description');
            $table->double('purchase_rate', 10, 2)->default(0);
            $table->double('sale_rate', 10, 2)->default(0);
            $table->double('default_d_commission', 10, 2)->default('0');
            $table->double('default_md_commission', 10, 2)->default('0');
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('default_assign')->default('0');
            $table->string('btn_text')->nullable();
            $table->tinyInteger('is_feature')->default(0);
            $table->string('image')->nullable();
            $table->string('banner')->nullable();
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
        Schema::dropIfExists('services');
    }
}
