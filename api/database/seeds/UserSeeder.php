<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert(
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => \Hash::make('admin'),
            ]
        );
    }
}
