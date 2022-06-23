<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainIdAndPlayerIdToPaybetrTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paybetr_transactions', function (Blueprint $table) {
            $table->bigInteger('domain_id')->after('uuid')->nullable()->index();
            $table->bigInteger('player_id')->after('domain_id')->nullable()->index();
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
        Schema::table('paybetr_transactions', function (Blueprint $table) {
            $table->dropColumn('domain_id');
            $table->dropColumn('player_id');
            //
        });
    }
}
