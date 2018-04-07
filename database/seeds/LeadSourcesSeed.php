<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\LeadSources;

class LeadSourcesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'lead_sources_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('lead_sources_manage');

        LeadSources::insert([
            1 => array(
                'id' => 1,
                'sort_no' => 1,
                'name' => 'Website',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            2 => array(
                'id' => 2,
                'sort_no' => 2,
                'name' => 'Social Media',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            3 => array(
                'id' => 3,
                'sort_no' => 3,
                'name' => 'Referral',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            4 => array(
                'id' => 4,
                'sort_no' => 4,
                'name' => 'Newspaper/Magazine',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            4 => array(
                'id' => 5,
                'sort_no' => 5,
                'name' => 'Other',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
        ]);
    }
}
