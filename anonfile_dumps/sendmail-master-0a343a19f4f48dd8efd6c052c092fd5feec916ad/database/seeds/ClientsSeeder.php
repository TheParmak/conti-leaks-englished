<?php

use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        foreach (range(0,100) as $i){
            $os = explode('.', array_rand(config('system')));
            $base64 = base64_encode($faker->ipv4."\t".$os[0]."\t".$os[1]);
            $data[$base64] = [
                "email_fail" => $faker->randomDigit,
                "email_response" => $faker->randomNumber(),
                "email_right" => $faker->randomNumber(),
                "email_sent" => $faker->randomNumber(),
                "last_activity" => $faker->unixTime(),
                "task_count" => $faker->randomDigit
            ];
        }
        Storage::put('online.json', json_encode($data));
    }
}
