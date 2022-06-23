<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaybetrWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paybetr_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('player_id')->index();
            $table->uuid('account_uuid')->index();
            $table->uuid('withdrawal_uuid')->nullable()->index();
            $table->uuid('transaction_uuid')->nullable()->index();
            $table->string('request_currency')->index();
            $table->string('converted_currency')->index();
            $table->string('address')->index();
            $table->decimal('amount', 16, 8);
            $table->decimal('converted_amount', 16, 8);
            $table->boolean('confirmed')->default(false)->index();
            $table->boolean('cancelled')->default(false)->index();
            $table->boolean('approved')->default(false)->index();
            $table->boolean('refunded')->default(false)->index();
            $table->boolean('sent')->default(false)->index();
            $table->string('txid')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paybetr_withdrawals');
    }
}
