<?php defined('SYSPATH') or die('No direct script access.');

class Pagination extends Kohana_Pagination
{
    
    public function getTotalItems()
    {
        return $this->total_items;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getItemsPerPage(){
        return $this->items_per_page;
    }

}