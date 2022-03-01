<?php

use Illuminate\Database\Seeder;

class SeederStatistics extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (range(1,10) as $a){
            $model = \App\ConnectionResults::create([
                'connection_id' => $a,
                'add_date' => $faker->dateTime(),
                'outlook_total_address' => $faker->randomDigit,
                'outlook_additional_address' => $faker->randomDigit,
                'outlook_sent_address' => $faker->randomDigit,
                'thunderbird_version' => $faker->randomDigit,
                'is_tb_addons_installed' => $faker->boolean,
                'outlook_email_blocked_by_name' => $faker->randomDigit,
                'outlook_email_blocked_by_domain' => $faker->randomDigit,
                'conn_addr_recv' => $faker->randomDigit,
                'conn_error_code' => $faker->randomDigit,
                'sys_ver' => $faker->text(18),
                'outlook_ver' => $faker->text(18),
                'outlook_platform' => $faker->text(8),
            ]);

            $mail_address = [];
            foreach(range(0,3) as $b){
                $mail_address[] = [
                    'address' => $faker->email,
                    'connection_id' => $a,
                ];
            }

            $model->mailAddress()->createMany($mail_address);
        }
    }
}
