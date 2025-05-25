<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
public function run()
{
    \App\Models\Role::insert([
        ['name' => 'Administrator', 'created_at'=>now(),'updated_at'=>now()],
        ['name' => 'User',          'created_at'=>now(),'updated_at'=>now()],
    ]);

    \App\Models\User::factory()->create([
        'name'    => 'Admin',
        'email'   => 'admin@example.com',
        'password'=> bcrypt('password'),
        'role_id' => Role::where('name','Administrator')->first()->id,
    ]);
}
}
