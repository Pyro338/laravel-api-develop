<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUserAffiliateAndStatusPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status_points');
            $table->dropColumn('affiliate');
            $table->bigInteger('affiliate_id')->after('global_admin')->nullable()->index();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_id');
            $table->bigInteger('affiliate')->after('global_admin')->nullable()->index();
            $table->decimal('status_points', 16, 8)->after('affiliate')->default(0);
            //
        });
    }
}
