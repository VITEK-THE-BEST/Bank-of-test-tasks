<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

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
        );
        Group::insert(
            ['name' => "1182б"],
        );
        Group::insert(
            ['name' => "1183б"],
        );
        Group::insert(
            ['name' => "1111б"],
        );
    }
}
