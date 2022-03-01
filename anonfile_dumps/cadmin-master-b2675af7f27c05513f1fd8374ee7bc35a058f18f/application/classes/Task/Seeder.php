<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Seed all tables
 * php index.php --task=seeder --tables=all
 *
 * Seed clients table. Counts - parameter, created count element:
 * php index.php --task=seeder --tables=clients --counts=count_client
 *
 * Create fake tables and clear counter.
 * php index.php --task=seeder --tables=all --clear_counter=true
 */
class Task_Seeder extends Minion_Task{

    /** @var Faker\Generator */
    private $faker;
    /** @var array */
    private $country;

    protected $_options = [
        'tables'   	=> NULL,
        'counts'	=> NULL,
        'clear_counter'	=> NULL,
    ];

    protected function _execute(array $params){
        if(Kohana::$environment != Kohana::DEVELOPMENT){
            Minion_CLI::write('You are not in the development environment');
            exit;
        }

        $tables = strtolower($params['tables']);

        if (!empty($params['clear_counter'])){ self::ClearCounter($tables); }

        $counts = !empty($params['counts']) ? $params['counts'] : 10 ;
        $this->faker = Faker\Factory::create();
        $this->country = Kohana::$config->load('country')->as_array();

        if($tables == 'all'){
            /* tables*/
            self::clients($counts); // Defaults is All - 10

		    }elseif($tables != NULL){
            call_user_func([__CLASS__, $tables], $counts);
        }
  }

	private function ClearCounter($table = null){
	    if(!$table || $table == 'all'){
            $counter = ORM::factory('Counter')->find_all();
            foreach ($counter as $i){
                $i->id = 0;
                $i->update();
            }
        }else{
            $counter = ORM::factory('Counter', ['name' => $table]);
            if($counter->loaded()){
                $counter->id = 0;
                $counter->update();
            }
        }
	}


  private function clients($count_elements){
        $countrys = Kohana::$config->load('country')->as_array();
    		$n=0;
    		$client_id_arr = [];
    		$counter_fixed=0;
    		for ($i=0; $i < $count_elements; $i++){
    			$n++;
      		$client_add = ORM::factory('Client');
    			$client_add->id_low = $this->faker->regexify('[0-9]{16}');
    			$client_add->id_high = $this->faker->regexify('[0-9]{16}');
          $client_add->name = $this->faker->word; // character varying(512)
          $client_add->ip = $this->faker->ipv4;
          $client_add->country = array_rand($countrys);
          $client_add->created_at = date('Y-m-d', time()-rand(300000, 500000));
          $client_add->last_activity = date('Y-m-d', time()-rand(86000, 86000));
          $client_add->group = $this->faker->word; // character varying(64)
          $client_add->userdefined = rand(0, 10000); // integer
          // Possible outdate
          //$client_add->devhash_1 = randomBigInt(9);
          //$client_add->devhash_2 = randomBigInt(9);
          //$client_add->devhash_3 = randomBigInt(9);
          //$client_add->devhash_4 = randomBigInt(9);
    			$client_add->create();
    			$client_id_arr[] = $client_add->id;
    			$counter_fixed++;
		    }
  		DB::query(Database::UPDATE, "UPDATE counter SET id=id+'".$counter_fixed."' WHERE name='clients' ")->execute();
  		Minion_CLI::write('Success created - '.$n.' clients!');
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

        if($bigint < $max && $bigint > $min){ return $bigint; }
        else { return $this->randomBigInt($length); }
    }

    private function getParamFromRandomRecord($table, $field){
        if(!is_array($field)){
            $field = [$field];
        }

        $record = DB::select_array($field)
            ->from($table)
            ->order_by(DB::expr('RANDOM()'))
            ->limit(1)
            ->execute()
            ->current();
        if(count($field) == 1){
            $record = $record[end($field)];
        }
        return $record;
    }
}
