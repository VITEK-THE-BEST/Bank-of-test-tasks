<?php

namespace Database\Seeders;

use App\Models\Group;
use Database\Factories\GroupFactory;
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
        GroupFactory::new()->createMany([
            ['name' => "1181б"],
            ['name' => "1182б"],
            ['name' => "1183б"],
            ['name' => "1111б"],
        ]);
    }
}
