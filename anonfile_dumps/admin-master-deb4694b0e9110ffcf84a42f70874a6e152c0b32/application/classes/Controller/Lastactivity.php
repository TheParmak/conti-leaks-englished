<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Lastactivity extends CheckAction{
	public function action_index(){
        $file_path_net_access = Kohana::find_file(
            'net_access',
            Auth::instance()->get_user()->id,
            'json'
        );

		$clients = DB::select(DB::expr('TRIM("country") AS country'), DB::expr('COUNT(*) AS cnt'), DB::expr('SUM(COUNT(name)) OVER() AS total_count'))
			->from('clients')
			->where('last_activity', 'BETWEEN', [
				DB::expr("NOW() - INTERVAL '10 MINUTE'"),
				DB::expr("NOW() + INTERVAL '10 MINUTE'")
			]);

        if($file_path_net_access) {
            $net_list = json_decode(
                file_get_contents($file_path_net_access)
            );
            $clients->and_where_open();
            foreach ($net_list as $nl) {
                $clients->or_where('group', 'LIKE', '%' . $nl . '%');
            }
            $clients->and_where_close();
        }

        $clients = $clients->group_by('country')
			->order_by('cnt', 'DESC')
            ->distinct(true)
			->execute()
			->as_array();

        $alpha_country = Kohana::$config->load('country')->as_array();
        foreach($clients as $k => $v){
            $c = $v['country'];
            $clients[$k]['country'] = isset($alpha_country[$c]) ? $alpha_country[$c] : $c;
        }

		$this->template->content = BladeView::factory('/lastactivity/index')
			->bind('clients', $clients);
	}
}