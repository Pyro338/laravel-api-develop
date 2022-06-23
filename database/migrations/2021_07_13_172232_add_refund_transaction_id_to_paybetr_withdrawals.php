<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundTransactionIdToPaybetrWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paybetr_withdrawals', function (Blueprint $table) {
            $table->uuid('refund_transaction_uuid')->after('transaction_uuid')->nullable();
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
            $table->dropColumn('refund_transaction_uuid');
            //
        });
    }
}
