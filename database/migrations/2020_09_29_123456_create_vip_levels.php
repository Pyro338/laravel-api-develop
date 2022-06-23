<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVipLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vip_levels', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->integer('level')->unsigned()->index();
            $table->string('name');
            $table->string('league');
            $table->decimal('amount_required', 12, 4);
            $table->string('color');
            $table->string('icon');
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
        Schema::drop('vip_levels');
    }
}
