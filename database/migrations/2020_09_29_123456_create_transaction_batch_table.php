<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_batch_queue', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->bigInteger('domain_id')->nullable();
            $table->bigInteger('player_id')->nullable();
            $table->string('handler', 255);
            $table->string('type');
            $table->integer('transaction_id')->unsigned();
            $table->string('transaction_type');
            $table->decimal('transaction_amount', 11, 4);
            $table->integer('tag_id');
            $table->integer('tag_group_id');
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
        Schema::drop('transaction_batch_queue');
    }
}
