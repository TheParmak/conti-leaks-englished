<?php

use App\Email;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach (range(0,10) as $i){
            Email::create([
                'title' => $faker->unique()->word,
                'from' => $faker->name(),
                'body' => $faker->text(),
            ]);
        }
    }
}
