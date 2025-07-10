<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('image');
            $table->string('gender', 10)->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('shop_name')->nullable()->after('address');
            $table->text('shop_address')->nullable()->after('shop_name');
            $table->string('aadhar_no', 20)->nullable()->after('shop_address');
            $table->string('pan_no', 20)->nullable()->after('aadhar_no');
            $table->string('aadhar_doc')->nullable()->after('pan_no');
            $table->string('pan_doc')->nullable()->after('aadhar_doc');
            $table->string('bank_proof_doc')->nullable()->after('pan_doc');
            $table->string('bank_name')->nullable()->after('bank_proof_doc');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_ifsc_code')->nullable()->after('bank_account_number');
        });
    }

    public function down()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'address',
                'shop_name',
                'shop_address',
                'aadhar_no',
                'pan_no',
                'aadhar_doc',
                'pan_doc',
                'bank_proof_doc',
                'bank_name',
                'bank_account_number',
                'bank_ifsc_code'
            ]);
        });
    }
};
