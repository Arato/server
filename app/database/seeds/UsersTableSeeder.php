<?php

use Illuminate\Database\Seeder;
use Underscore\Types\Arrays;

class UsersTableSeeder extends Seeder
{
    public function run()
    {

        $emails = [
            'pierre.baron@alyacom.fr',
            'emmanuel.gendron@alyacom.fr',
            'thibaut.brier@alyacom.fr',
            'yoann.hamon@alyacom.fr'
        ];

        Arrays::each($emails, function ($email) {
            User::create([
                'email'    => $email,
                'password' => Hash::make('password'),
            ]);
        });
    }
}
