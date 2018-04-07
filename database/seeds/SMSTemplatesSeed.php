<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\SMSTemplates;

class SMSTemplatesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'sms_templates_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('sms_templates_manage');

        SMSTemplates::insert([
            1 => array(
                'id' => 1,
                'name' => 'Appointment SMS',
                'content' => 'Dear ##patient_name##,
Your appointment details are as follows:
Appointment Type: General Consultation
Doctor: ##doctor_name##
Date: ##appointment_date##
Time: ##appointment_time##
Clinic Contact #: ##fdo_phone##
Location: ##centre_address##
Directions: ##centre_google_map##

If you may have any queries, please call ##head_office_phone##',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            2 => array(
                'id' => 2,
                'name' => 'Leads SMS',
                'content' => 'Seeing is believing. Watch real celebrity endorsements & Customer success stories and get inspired by men and women who completely transformed their Face and Bodies with our next generation non-surgical and non-invasive technologies.',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
        ]);

    }
}
