<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php if ( ! Kohana::$profiling ) : ?>
<div class="container-fluid">
    <div class="alert alert-danger small">
        <strong>Warning!</strong> Kohana profiling is disabled. Please, enabled it in bootstrap.php:<br /><br />
        &nbsp;&nbsp;&nbsp;&nbsp;Kohana::init(array(<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'profile' => true,<br />
        &nbsp;&nbsp;&nbsp;&nbsp;));<br />
    </div>
</div>
<?php endif; ?>

<?php include(SYSPATH . 'views/profiler/stats' . EXT);
