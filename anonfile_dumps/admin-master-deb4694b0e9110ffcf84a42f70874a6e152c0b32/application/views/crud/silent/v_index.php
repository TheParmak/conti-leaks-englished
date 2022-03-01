<?php

echo View::factory('crud/silent/template/v_table')
	->bind('model', $model);

echo View::factory('crud/silent/template/v_modal')
    ->bind('errors', $errors);
echo View::factory('crud/silent/template/v_script');