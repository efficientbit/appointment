<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Cities;

class CitiesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'cities_manage']);

        // Assign Permission
        $role = Role::findOrFail(1);
        $role->givePermissionTo('cities_manage');

        Cities::insert([
            1 => array(
                'id' => 1,
                'name' => 'Lahore',
                'sort_number'=>'1',
                'is_featured'=> 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            2 => array(
                'id' => 2,
                'name' => 'Karachi',
                'sort_number'=>'2',
                'is_featured'=> 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            3 => array(
                'id' => 3,
                'name' => 'Islamabad',
                'sort_number'=>'3',
                'is_featured'=> 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
            4 => array(
                'id' => 4,
                'name' => 'Peshawar',
                'sort_number'=>'4',
                'is_featured'=> 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ),
        ]);

    }
}
