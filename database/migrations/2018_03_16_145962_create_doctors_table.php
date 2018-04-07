<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500);
            $table->text('profile_url')->nullable();

            $table->unsignedInteger('city_id');
            $table->unsignedInteger('location_id');

            $table->unsignedTinyInteger('active')->default(1);

            $table->timestamps();
            $table->softDeletes();

            // Manage Foreing Key Relationshops
            $table->foreign('city_id')
                ->references('id')
                ->on('cities');
            $table->foreign('location_id')
                ->references('id')
                ->on('locations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}