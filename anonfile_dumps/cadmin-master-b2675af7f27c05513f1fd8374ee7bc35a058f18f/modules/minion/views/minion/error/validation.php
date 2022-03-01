Parameter Errors:
<?php foreach ($errors as $parameter => $error): ?>
    <?php echo $parameter; ?> - <?php echo $error; ?> 
<?php endforeach; ?>

Run

    php index.php --task=<?php echo $task?> --help

for more help