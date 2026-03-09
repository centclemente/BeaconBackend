<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
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
                "name" => "Admin",
                "access_permissions" =>([
                    "manage_users",
                    "manage_roles",
                    "import",
                    "manage_status",
                    "manage_teams",
                    "manage_charging",
                ]),
            ],
            [
                "name" => "User",
                "access_permissions" => ([
                    "view_systems",
                    "view_progress",
                    "export",
                    "import",
                ]),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
    
}
