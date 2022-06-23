<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainIdToGameCenterTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_center_transactions', function (Blueprint $table) {
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
        Schema::table('game_center_transactions', function (Blueprint $table) {
            $table->dropColumn('domain_id');
            //
        });
    }
}
