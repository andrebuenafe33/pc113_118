<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            ['name' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'manager', 'created_at' => $now, 'updated_at' =>$now],
            ['name' => 'staff', 'created_at' => $now, 'updated_at' =>$now]
        ];
        DB::table('roles')->insert($roles);
    }
}
