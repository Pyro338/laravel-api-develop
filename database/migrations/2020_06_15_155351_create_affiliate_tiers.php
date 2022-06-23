<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateTiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_tiers', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->integer('tier')->nullable();
            $table->string('name')->nullable();
            $table->decimal('amount_required', 11, 4);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['created_at', 'updated_at', 'tier', 'domain_id'], 'compound');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('affiliate_tiers');
    }
}
