<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \App\Admin::create(
            [
                'name' => 'ADMIN',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
            ]
        );
    }
}
