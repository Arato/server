<?php

use Illuminate\Database\Seeder;
use Underscore\Types\Arrays;

class UsersTableSeeder extends Seeder
{
    public function run()
    {

        $emails = [
            'user1@email.com',
            'user2@email.com'
        ];

        Arrays::each($emails, function ($email) {
            User::create([
                'email'    => $email,
                'password' => Hash::make('password')
            ]);
        });
    }
}
