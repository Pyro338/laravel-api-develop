<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaybetrTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paybetr_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('category')->index();
            $table->string('txid')->index();
            $table->string('recipient_address')->index();
            $table->string('currency')->index();
            $table->decimal('amount', 16, 8);
            $table->decimal('converted_amount', 16, 8);
            $table->boolean('unconfirmed')->index();
            $table->boolean('confirmed')->index();
            $table->boolean('complete')->index();
            $table->uuid('external_id')->nullable();
            $table->boolean('credited')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paybetr_transactions');
    }
}
