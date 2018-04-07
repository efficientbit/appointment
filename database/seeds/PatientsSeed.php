<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\LeadStatuses;

class PatientsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'patients_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('patients_manage');
    }
}
