<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToGameCenterTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_center_transactions', function (Blueprint $table) {
            $table->string('currency')->after('amount')->nullable()->index();
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
            $table->dropColumn('currency');
            //
        });
    }
}
