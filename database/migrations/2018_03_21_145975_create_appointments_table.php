<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');

            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->text('reason')->nullable();

            $table->unsignedInteger('lead_id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('doctor_id');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('appointment_status_id')->nullable();
            $table->unsignedInteger('cancellation_reason_id')->nullable();
            $table->unsignedInteger('treatment_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unsignedTinyInteger('active')->default(1);
            $table->unsignedTinyInteger('msg_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Manage Foreing Key Relationshops
            $table->foreign('lead_id')
                ->references('id')
                ->on('leads');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients');
            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors');
            $table->foreign('city_id')
                ->references('id')
                ->on('cities');
            $table->foreign('location_id')
                ->references('id')
                ->on('locations');
            $table->foreign('treatment_id')
                ->references('id')
                ->on('treatments');
            $table->foreign('appointment_status_id')
                ->references('id')
                ->on('appointment_statuses');
            $table->foreign('cancellation_reason_id')
                ->references('id')
                ->on('cancellation_reasons');
            $table->foreign('created_by')
                ->references('id')
                ->on('users');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users');

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