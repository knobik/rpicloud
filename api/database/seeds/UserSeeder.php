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
                'email' => \Str::random(10).'@gmail.com',
                'password' => \Hash::make('admin'),
            ]
        );
    }
}
