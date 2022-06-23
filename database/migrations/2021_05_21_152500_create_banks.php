<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('user_id')->index();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('hidden')->default(false)->index();
            $table->boolean('transferable')->default(false)->index();
            $table->boolean('relaxed_balances')->default(false)->index();
            $table->string('currency')->index();
            $table->string('currency_type')->index();
            $table->string('deposit_currency')->nullable()->index();
            $table->boolean('playable')->default(false)->index();
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
        Schema::dropIfExists('banks');
    }
}
