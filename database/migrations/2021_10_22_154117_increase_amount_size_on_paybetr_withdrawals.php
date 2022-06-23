<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseAmountSizeOnPaybetrWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paybetr_withdrawals', function (Blueprint $table) {
            //
            $table->decimal('amount', 20, 8)->change();
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
            //
        });
    }
}
