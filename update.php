<?php

set_time_limit(600);
date_default_timezone_set('Europe/Berlin');
include('classes/beacon_repository.php');
include('templates/functions.php');
$repository = new beacon_repository();
echo implode("\r\n", $repository->errorMessages);
$repository->update();

?>