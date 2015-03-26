<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class AlertsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $userIds = User::lists('id');

        foreach (range(1, 30) as $index) {
            Alert::create([
                'title'   => $faker->sentence(2),
                'content' => $faker->paragraph(),
                'price'   => $faker->randomFloat(2, 0),
                'user_id' => $faker->randomElement($userIds)
            ]);
        }
    }
}
