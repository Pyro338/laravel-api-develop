<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_media_group', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->string('title');
            $table->string('landing_page_url');
            $table->integer('weight');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['created_at', 'updated_at', 'domain_id'], 'compound');
        });
        Schema::create('affiliate_media_item', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->integer('media_group_id')->unsigned();
            $table->string('title');
            $table->string('filepath');
            $table->integer('weight');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['created_at', 'updated_at', 'domain_id'], 'compound');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('affiliate_media_group');
        Schema::drop('affiliate_media_item');
    }
}
