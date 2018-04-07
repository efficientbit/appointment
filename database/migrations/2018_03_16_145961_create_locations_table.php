<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500);
            $table->string('fdo_name', 500)->nullable();
            $table->string('fdo_phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->text('google_map')->nullable();
            $table->unsignedInteger('city_id');
            $table->unsignedTinyInteger('active')->default(1);
            $table->timestamps();
            $table->softDeletes();

            // Manage Foreing Key Relationshops
            $table->foreign('city_id')
                ->references('id')
                ->on('cities');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}