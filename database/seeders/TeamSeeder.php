<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = [
            [
                "name" => "Omega",
                "code" => "OMG",
            ],
            [
                "name" => "Alpha",
                "code" => "ALP",
            ],
            [
                "name" => "Delta",
                "code" => "DLT",
            ],
            [
                "name" => "Beta",
                "code" => "BTA",
            ],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}
