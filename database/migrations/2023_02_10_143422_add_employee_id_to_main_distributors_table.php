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
        Schema::table('main_distributors', function (Blueprint $table) {
            $table->bigInteger('employee_id')->nullable()->after('user_balance');
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->bigInteger('employee_id')->nullable()->after('user_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_distributors', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
