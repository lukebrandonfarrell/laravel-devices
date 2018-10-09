<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LbfCreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('auth_token');
            $table->string('uuid');
            $table->tinyInteger('platform'); // 1 - iOS (APNS), 2 - Android (GCM/FCM) // 3 - Website
            $table->text('push_token')->nullable();
            $table->timestamps();

            /* Index these values for search optimisation */
            $table->unique('uuid');
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
