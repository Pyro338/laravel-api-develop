<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrupalMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drupal_mappings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('drupal_id')->index();
            $table->uuid('user_uuid')->index();
            $table->bigInteger('domain_id')->index();
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
        Schema::dropIfExists('drupal_mappings');
    }
}
