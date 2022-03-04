<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Datafiles extends CheckAction {

	/* TODO переписать эту шляпу после кандидата с орм на db::query и почистить шаблон */
    public function action_index() {

        $datafiles = ORM::factory('Datafiles');

        $total_items = $datafiles->count_all();

        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page'
            ],
        ]);

        $datafiles = $datafiles
            ->order_by('datetime', 'desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();

        $this->template->content = View::factory("datafiles/v_index", [
            'datafiles' => $datafiles,
            'total' => $total_items,
            'pagination' => $pagination,
        ]);
    }

}
