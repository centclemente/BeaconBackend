<?php

namespace Database\Seeders;

use App\Models\Charging;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChargingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Sales', 'code' => 'SLS'],
            ['name' => 'Customer Service', 'code' => 'CS'],
            ['name' => 'Legal', 'code' => 'LEG'],
            ['name' => 'Research and Development', 'code' => 'RND'],
            ['name' => 'Administration', 'code' => 'ADM'],
        ];

        foreach ($departments as $department) {
            Charging::create($department);
        }
    }
}
