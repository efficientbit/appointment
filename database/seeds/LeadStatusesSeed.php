<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\LeadStatuses;

class LeadStatusesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'lead_statuses_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('lead_statuses_manage');

        LeadStatuses::insert([
            1 => array(
                'id' => 1,
                'sort_no' => 1,
                'name' => 'Open',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            2 => array(
                'id' => 2,
                'sort_no' => 2,
                'name' => 'Contacted',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            3 => array(
                'id' => 3,
                'sort_no' => 3,
                'name' => 'Converted',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            4 => array(
                'id' => 4,
                'sort_no' => 4,
                'name' => 'Not Interested',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            5 => array(
                'id' => 5,
                'sort_no' => 5,
                'name' => 'Junk',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
        ]);
    }
}
