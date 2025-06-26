<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banner_admins', function (Blueprint $table) {
            $table->string('banner_for');
            $table->string('url')->nullable();
            $table->tinyInteger('is_special')->default(0)->comment('(0=expire, 1=active)');
        });
    }

    public function down()
    {
        Schema::table('banner_admins', function (Blueprint $table) {
            $table->dropColumn('banner_for');
            $table->dropColumn('url');
            $table->dropColumn('is_special');
        });
    }
};
