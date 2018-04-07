<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Locations;

class LeadsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'leads_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('leads_manage');
    }
}
