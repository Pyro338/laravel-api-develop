<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVipRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vip_rates', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->integer('level')->unsigned()->index();
            $table->string('type')->index();
            $table->integer('tag_vocabulary_id')->unsigned()->index();
            $table->decimal('value', 11, 4);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vip_rates');
    }
}
