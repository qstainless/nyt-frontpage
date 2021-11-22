<?php
declare(strict_types=1);

// include the helper functions
include __DIR__ . '/functions.php';

// Get the frontpage
$frontpage = get_frontpage();
?>

<meta http-equiv="refresh" content="21600">
<body style="margin-top:-100px;margin-left:-27px">
<img width=102% src="<?= $frontpage ?>" alt="NY Times Frontpage for <?= date('l, F j, Y') ?>"/>
</body>
