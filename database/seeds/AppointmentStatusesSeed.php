<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\AppointmentStatuses;

class AppointmentStatusesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'appointment_statuses_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('appointment_statuses_manage');

        AppointmentStatuses::insert([
            1 => array(
                'id' => 1,
                'sort_no' => 1,
                'name' => 'Pending',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            2 => array(
                'id' => 2,
                'sort_no' => 1,
                'name' => 'Arrived',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            3 => array(
                'id' => 3,
                'sort_no' => 3,
                'name' => 'Not Show',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
        ]);
    }
}
