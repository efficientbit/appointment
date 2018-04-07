<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeed::class);
        $this->call(RoleSeed::class);
        $this->call(UserSeed::class);
        // Cities Seeder
        $this->call(CitiesSeed::class);
        // Locations Seeder
        $this->call(LocationsSeed::class);
        // Doctors Seeder
        $this->call(DoctorsSeed::class);
        // Appointment Statuses Seeder
        $this->call(AppointmentStatusesSeed::class);
        // Appointment Statuses Seeder
        $this->call(CancellationReasonsSeed::class);
        // Appointments Seeder
        $this->call(AppointmentsSeed::class);
        // Lead Sources Seeder
        $this->call(LeadSourcesSeed::class);
        // Treatments Seeder
        $this->call(TreatmentsSeed::class);
        // Lead Statuses Seeder
        $this->call(LeadStatusesSeed::class);
        // Patients Seeder
        $this->call(PatientsSeed::class);
        // Leads Seeder
        $this->call(LeadsSeed::class);
        // Settings Seeder
        $this->call(SettingsSeed::class);
        // SMS Templated Seeder
        $this->call(SMSTemplatesSeed::class);
    }
}
