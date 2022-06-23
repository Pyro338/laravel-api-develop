<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateConversions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('domain_id')->index();
            $table->bigInteger('player_id')->index();
            $table->bigInteger('affiliate_id')->index();
            $table->string('template_id')->nullable();
            $table->string('custom_id')->nullable();
            $table->string('promo_code')->nullable();
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
        Schema::dropIfExists('affiliate_conversions');
    }
}
