<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserBalanceToRetailerTable extends Migration
{
    public function up()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->double('user_balance', 10, 2)
                ->default(0)
                ->after('status')
                ->comment('User Current Balance.');
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->double('user_balance', 10, 2)
                ->default(0)
                ->after('status')
                ->comment('User Current Balance.');
        });

        Schema::table('main_distributors', function (Blueprint $table) {
            $table->double('user_balance', 10, 2)
                ->default(0)
                ->after('status')
                ->comment('User Current Balance.');
        });
    }

    public function down()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn('user_balance');
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('user_balance');
        });

        Schema::table('main_distributors', function (Blueprint $table) {
            $table->dropColumn('user_balance');
        });
    }
}
