<?php
set_time_limit(600);
date_default_timezone_set('Europe/Berlin');
require_once '../vendor/autoload.php';

$repository = new \HAB\BeaconRepository(false);
$gnd = null;
if (empty($_GET['gnd']) and empty($_POST['gnd'])) {
    $gnd = new \HAB\GND('');
}
elseif (empty($_POST['gnd'])) {
    $gnd = new \HAB\GND($_GET['gnd']);
}
else {
    $gnd = new \HAB\GND($_POST['gnd']);
}
$gndRequest = null;
if (!empty($gnd->valid)) {
	$gndRequest = new \HAB\RequestGND($gnd);
}

$loader = new \Twig\Loader\FilesystemLoader('../templates/twig');
$twig = new \Twig\Environment($loader, ['debug' => true], ['cache' => '../templates/twig/twig-cache']);
$template = $twig->load('index.twig');
echo $template->render([
		'title' => 'Personeninformation',
		'gnd' => $gnd->id,
		'gndReq' => $gndRequest,
		'repository' => $repository,
		'types' => $repository->getTypeArray(),
		'linksHAB' => $repository->getSelectedLinks($gnd, true, '_blank'),
		'linksExt' => $repository->getSelectedLinks($gnd, false, '_blank')
	]);
?>