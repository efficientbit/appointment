<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Doctors;

class DoctorsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'doctors_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('doctors_manage');
    }
}
