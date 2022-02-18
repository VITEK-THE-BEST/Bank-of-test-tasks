<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::insert(
            ['name' => "1181б"],
            ['name' => "1182б"],
            ['name' => "1183б"],
            ['name' => "1111б"],
        );
    }
}
