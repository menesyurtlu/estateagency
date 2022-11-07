<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->integer('trip_distance');
            $table->integer('trip_duration');
            $table->integer('user_id');
            $table->integer('contact_id');
            $table->dateTime('datetime');
            $table->dateTime('estimated_departure');
            $table->dateTime('estimated_arrival_to_office');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('appointments');
    }
}
