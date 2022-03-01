<?php defined('SYSPATH') or die('No direct script access.');

class Task_Nat extends Minion_Task{
    
    protected function _execute(array $params){
        $limit = 20;
        $updated_at = DB::expr('extract(epoch FROM q.updated_at) AS updated_at');
        $subquery = DB::select('client_id', DB::expr('max(updated_at) AS updated_at'))
            ->from('storage')
            ->where('id', '>', Helper::getCounter('nat'))
            ->and_where('key', '=', 'NAT status')
            ->and_where_open()
                ->where('value', '=', 'client is behind NAT')
                ->or_where('value', '=', 'client is not behind NAT')
            ->and_where_close()
            ->group_by('client_id');
        $records = DB::select(DB::expr('q.id as storage_id'), DB::expr('q.client_id as id'),  DB::expr('q.value as nat'), $updated_at)
            ->from([$subquery, 's'])
            ->join(['storage', 'q'], 'INNER')
            ->on('q.client_id', '=', 's.client_id')
            ->on('q.updated_at', '=', 's.updated_at')
            ->order_by('q.id')
            ->limit($limit)
            ->execute()
            ->as_array();

        if(!empty($records)) {
            $query = DB::replace('nat', array_keys($records[0]));
            foreach ($records as $r){
                $r['nat'] = ($r['nat'] == 'client is behind NAT');
                $query->values($r);
            }
            $query->execute(Helper::getCurrentSphinx());

            $value = end($records);
            Helper::updCounter($value['storage_id'], 'nat');
        }else{
            sleep(5);
        }
    }
}