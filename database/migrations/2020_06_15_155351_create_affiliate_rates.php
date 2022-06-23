<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_rates', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->integer('tier')->nullable();
            $table->integer('tag_vocabulary_id')->nullable();
            $table->decimal('value', 11, 4);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['created_at', 'updated_at', 'tier', 'tag_vocabulary_id', 'domain_id'], 'compound');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('affiliate_rates');
    }
}
