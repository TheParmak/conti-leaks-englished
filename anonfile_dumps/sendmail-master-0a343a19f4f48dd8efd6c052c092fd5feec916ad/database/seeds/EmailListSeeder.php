<?php

use Illuminate\Database\Seeder;

class EmailListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(App::environment() == 'local'){
            array_map('unlink', glob(storage_path('emails/*')));
        }

        $faker = Faker\Factory::create();
        $json = [];

        foreach (range(0,10) as $i){
            $name = $faker->unique()->word;
            Storage::disk('emails')->put(
                base64_encode($name),
                $this->randomFile()
            );

            $json[base64_encode($name)] = [
                'count' => $faker->randomNumber(),
                'size' => $faker->randomNumber(),
            ];
        }
        Storage::put('email_list.json', json_encode($json));
    }

    private function randomFile(){
        exec('head -c 1024 /dev/urandom', $file);
        return pg_escape_bytea($file[0]);
    }
}
