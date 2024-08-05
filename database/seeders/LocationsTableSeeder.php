<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ['name' => 'Atlanta'],
            ['name' => 'Columbus'],
            ['name' => 'Augusta'],
            ['name' => 'Macon']
            // Add more locations as needed, but exclude Atlanta, Columbus, Augusta, Macon
        ];

        DB::table('locations')->insert($locations);
    }
}
