<?php

set_time_limit(600);
date_default_timezone_set('Europe/Berlin');
require_once '../vendor/autoload.php';
$repository = new HAB\BeaconRepository(false);
echo implode("\r\n", $repository->errorMessages);
$repository->update();

?>
