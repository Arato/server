<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationentriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificationentries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id');
            $table->string('field', 255);
            $table->string('previousValue', 255)->nullable();
            $table->string('newValue', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notificationentries', function (Blueprint $table) {
            Schema::drop('notificationentries');
        });
    }

}
