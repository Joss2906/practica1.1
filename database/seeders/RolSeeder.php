<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $rol = new Role();
        $rol->name = 'admin';
        $rol->save();

        $rol = new Role();
        $rol->name = 'user';
        $rol->save();
    }
}
