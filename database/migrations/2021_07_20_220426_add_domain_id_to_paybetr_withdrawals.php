<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainIdToPaybetrWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paybetr_withdrawals', function (Blueprint $table) {
            $table->bigInteger('domain_id')->after('uuid')->nullable()->index();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paybetr_withdrawals', function (Blueprint $table) {
            $table->dropColumn('domain_id');
            //
        });
    }
}
