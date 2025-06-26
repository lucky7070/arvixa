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
        Schema::table('service_pancards', function (Blueprint $table) {
            $table->tinyInteger('useFrom')->default(1)->after('id')->comment("1 : Web, 2 : App");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_pancards', function (Blueprint $table) {
            $table->dropColumn('useFrom');
        });
    }
};
