<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseConvertedAmountSizeOnPaybetrWithdrawals extends Migration
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
            $table->decimal('converted_amount', 20, 8)->change();
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
