<?php defined('SYSPATH') or die('No direct script access.');

class Task_Sysinfo extends Minion_Task{

    /**
     * @param array $params
     * @throws Kohana_Exception
     */
    protected function _execute(array $params){
        $limit = 100;
        $sysinfo = Kohana::$config->load('select.sysinfo');
        $sysinfo_all = Kohana::$config->load('select.sysinfo_all');

        foreach ($sysinfo as $item){
            $path = APPPATH.'sysinfo_'.$item.'.json';
            if(!file_exists($path)){
                file_put_contents($path, json_encode([]));
            }

            $query = DB::select('client_id', 'id')
                ->from('module_data')
                ->where('id', '>', Helper::getCounter('sysinfo:'.$item))
                ->and_where_open();

            foreach ($sysinfo_all[$item] as $i){
                $query->or_where('data', 'LIKE', '%'.$i.'%');
            }

            $records = $query->and_where_close()
                ->limit($limit)
                ->execute()
                ->as_array();

            Minion_CLI::write($item.'('.date("Y-m-d H:i:s").') - '.count($records));

            if(!empty($records)) {
                $data = json_decode(file_get_contents($path), true);
                $data = array_merge($data, Arr::path($records, ['*', 'client_id']));
                file_put_contents($path, json_encode($data));

                $end = end($records);
                Helper::updCounter($end['id'], 'sysinfo:'.$item);
            }else{
                sleep(5);
            }
        }
    }
}