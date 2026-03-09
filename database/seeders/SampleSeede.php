<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SampleSeede extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                "name" => "Sample",
                "access_permissions" =>([
                    "Test",
                    "Test",
                    "Test",
                    
                ]),
            ]
        ];
         foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
