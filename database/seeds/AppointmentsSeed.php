<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppointmentsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'appointments_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('appointments_manage');
    }
}
