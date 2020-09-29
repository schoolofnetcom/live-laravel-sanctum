<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Criação do usuário teste
        factory(\App\User::class, 1)->create([
            "name" => "Thiago",
            "email" => "son@admin.com"
        ]); // password
        // $this->call(UserSeeder::class);
    }
}
