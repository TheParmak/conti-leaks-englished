<?php defined('SYSPATH') or die('No direct script access.');

class Task_Seeder extends Minion_Task{

    private $faker;
    private $country;

    protected $_options = [
        'tables'   => NULL,
        'truncate' => NULL,
    ];

    /*
     * Example:
     * php index.php --task=seeder --tables=clients_events
     */
    protected function _execute(array $params){
        $tables = strtolower($params['tables']);
        $truncate = strtolower($params['truncate']);
        $this->faker = Faker\Factory::create();
        $this->country = Kohana::$config->load('country')->as_array();

        if($tables == 'all'){
            self::links();
            self::commands_idle();
            self::users();
            self::clients();
            self::configs();
            self::commands();
            self::storage();
            self::files();
            self::clients_log();
            self::module_data();
            self::apikey();
            self::apilog();
            self::backconnservers();
            self::clients_events();
            self::commands_event();
        }elseif($tables != NULL){
            call_user_func([__CLASS__, $tables]);
        }

        if($truncate == 'all'){
            // TODO
        }elseif($truncate != NULL){
            DB::query(Database::DELETE, 'TRUNCATE TABLE '.$truncate.' RESTART IDENTITY;');
        }
    }

    private function users(){
        $user = ORM::factory('User');
        $user->username = 'root';
        $user->password = 'ANSs67dgd8ugixg@k';
        $user->save();
        $user->add('roles', [
            ORM::factory('Role', ['name' => 'login']),
            ORM::factory('Role', ['name' => 'admin'])
        ]);
    }

    private function backconnservers(){
        foreach(range(0,10) as $i) {
            $model = ORM::factory('Server');
            $model->ip = $this->faker->ipv4;
            $model->port = $this->faker->randomNumber;
            $model->password1 = $this->faker->password;
            $model->password2 = $this->faker->password;
            $model->save();
        }
    }

    private function clients(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Client');
            $model->name = $this->faker->regexify('[A-Z0-9]{3,8}[_][A-Z][0-9]{6}');
            $model->group = $this->faker->word;
            $model->importance = 0;
            $model->created_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->logged_at = $this->faker->dateTimeBetween($model->created_at, 'now')->format('Y-m-d H:i:s');
            $model->id_low = $this->randomBigInt(19);
            $model->id_high = $this->randomBigInt(19);
            $model->ip = $this->faker->ipv4;
            $model->sys_ver = $this->faker->randomElement(Kohana::$config->load('faker.OS'));
            $model->country = array_rand($this->country);
            $model->client_ver = $this->randomBigInt(4);
            $model->userdefined = 0;
            $model->last_activity = $this->faker->dateTimeBetween($model->created_at, 'now')->format('Y-m-d H:i:s');
            $model->devhash_1 = $this->randomBigInt(19);
            $model->devhash_2 = $this->randomBigInt(19);
            $model->devhash_3 = $this->randomBigInt(19);
            $model->devhash_4 = $this->randomBigInt(19);
            $model->save();
        }
    }

    private function configs(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Config');
            $model->version = $this->faker->randomNumber(6);
            $model->data = $this->randomFile();
            $model->group = $this->faker->word;
            $model->sys_ver = $this->faker->randomElement(Kohana::$config->load('faker.OS'));
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->country = $this->faker->currencyCode;
            $model->save();
        }
    }

    private function commands(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Command');
            $model->params = $this->faker->word;
            $model->incode = $this->faker->randomDigit;
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->save();
        }
    }

    private function storage(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Var'); // TODO rename - Storage
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->updated_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->key = $this->faker->word;
            $model->value = $this->faker->word;
            $model->save();
        }

        foreach (range(0,10) as $i){
            $model = ORM::factory('Var');
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->updated_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->key = 'NAT status';
            $model->value = 'client is behind NAT';
            $model->save();
        }

        foreach (range(0,10) as $i){
            $model = ORM::factory('Var');
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->updated_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->key = 'NAT status';
            $model->value = 'client is not behind NAT';
            $model->save();
        }
    }

    private function files(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('File');
            $model->group = $this->faker->word;
            $model->country = $this->faker->currencyCode;
            $model->sys_ver = $this->faker->randomElement(Kohana::$config->load('faker.OS'));
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->priority = $i;
            $model->filename = $this->faker->word;
            $model->data = $this->randomFile();

            $model->save();
        }
    }

    private function links(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Link');
            $model->url = $this->faker->url;
            $model->created_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->expiry_at = $this->faker->dateTimeBetween($model->created_at, 'now')->format('Y-m-d H:i:s');
            $model->group = $this->faker->word;
            $model->country = $this->faker->currencyCode;
            $model->sys_ver = $this->faker->randomElement(Kohana::$config->load('faker.OS'));
            $model->importance_low = $this->faker->numberBetween(-100, 100);
            $model->importance_high = $this->faker->numberBetween(-100, 100);
            $model->userdefined_low = $this->faker->numberBetween(-100, 100);
            $model->userdefined_high = $this->faker->numberBetween(-100, 100);
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->save();
        }
    }

    private function commands_idle(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Idlecommands');
            $model->params = $this->faker->word;
            $model->count = $this->faker->randomNumber();
            $model->sys_ver = $this->faker->randomElement(Arr::merge(Kohana::$config->load('faker.OS'), ['*']));
            $model->group = $this->faker->word;
            $model->importance_low = $this->faker->numberBetween(-100, 100);
            $model->importance_high = $this->faker->numberBetween(-100, 100);
            $model->userdefined_low = $this->faker->numberBetween(-100, 100);
            $model->userdefined_high = $this->faker->numberBetween(-100, 100);
            $model->country_1 = $this->faker->currencyCode;
            $model->country_2 = $this->faker->currencyCode;
            $model->country_3 = $this->faker->currencyCode;
            $model->country_4 = $this->faker->currencyCode;
            $model->country_5 = $this->faker->currencyCode;
            $model->country_6 = $this->faker->currencyCode;
            $model->country_7 = $this->faker->currencyCode;
            $model->incode = $this->faker->numberBetween(0, 100);

            $model->save();
        }
    }

    private function clients_log(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Log');
            $model->client_id = $this->faker->numberBetween(0, 100);
            $model->created_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->type = $this->faker->numberBetween(0, 2);
            $model->info = $this->faker->text();
            $model->command = $this->faker->word;

            $model->save();
        }
    }

    private function module_data(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Datafiles');
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->name = $this->faker->word;
            $model->created_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->ctl = $this->faker->word;
            $model->ctl_result = $this->faker->word;
            $model->aux_tag = $this->faker->word;
            $model->data = $this->randomFile();

            $model->save();
        }
    }

    private function apikey(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Apikey');
            $model->commands_allowed = $this->faker->word;
            $model->ip = $this->listIps();
            $model->apikey = $this->faker->word;
            $model->pass = $this->faker->word;

            $model->save();
        }
    }

    private function apilog(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Apilog');
            $model->apikey = $this->faker->word;
            $model->apikey_id = $this->faker->numberBetween(1,100);
            $model->ip = $this->faker->ipv4;
            $model->command = $this->faker->word;
            $model->time = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->type = $this->faker->word;

            $model->save();
        }
    }

    private function clients_events(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Client_Event');
            $model->client_id = $this->faker->numberBetween(1,100);
            $model->created_at = $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s');
            $model->module = $this->faker->word;
            $model->event = $this->faker->word;
            $model->tag = $this->faker->word;
            $model->info = $this->faker->text();
            $model->data = $this->randomFile();

            $model->save();
        }
    }

    private function commands_event(){
        foreach(range(0,100) as $i){
            $model = ORM::factory('Command_Event');
            $model->incode = $this->faker->randomDigit;
            $model->params = $this->faker->word;
            $model->module = $this->faker->word;
            $model->event = $this->faker->word;
            $model->info= '.*';
            $model->interval = $this->faker->numberBetween(0,10);

            $model->save();
        }
    }



    /* HELPERS */
    private function listIps(){
        $list = '';
        foreach(range(0, $this->faker->randomDigitNotNull) as $i){
            $list .= $this->faker->ipv4.';';
        }
        return substr($list, 0, -1);
    }

    private function randomFile(){
        exec('head -c 100000 /dev/urandom', $file);
        return pg_escape_bytea($file[0]);
    }

    private function randomBigInt($length){
        $min = -9223372036854775808;
        $max = 9223372036854775807;
        $bigint = $this->faker->regexify('[0-9]{'.$length.'}');

        if($bigint < $max && $bigint > $min){
            return $bigint;
        }

        return $this->randomBigInt($length);
    }
}