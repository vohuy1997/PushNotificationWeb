<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_app', function (Blueprint $table) {
            $table->increments('id');
            $table->string('deviceName');
            $table->string('serialNumber');
            $table->string('operationSystem');
            $table->string('versionCode');
            $table->string('versionBuild');
            $table->string('deviceType');
            $table->string('bundleID');
            $table->string('fcmToken')->nullable();;
        });
    }

    /**ode
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_app');
    }
}
