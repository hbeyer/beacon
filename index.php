<?php
set_time_limit(600);
include('classes/beacon_repository.php');
include('classes/gnd_request.php');
include('templates/functions.php');
$repository = new beacon_repository;
if (empty($_GET['gnd']) and empty($_POST['gnd'])) {
    $gnd = null;
}
elseif (empty($_POST['gnd'])) {
    $gnd = substr($_GET['gnd'], 0, 15);
}
else {
    $gnd = substr($_POST['gnd'], 0, 15);
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Personeninformation</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/css/affix.css" />
        <link rel="stylesheet" href="assets/css/proprietary.css" />
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/proprietary.js"></script>
    </head>
    <body>
<?php makeNavigation(); ?> 
        <div class="container" style="min-height:1000px;margin-top:80px;">
            
            <div class="row">
            <?php
            if ($gnd) {
                $gndRequest = new gnd_request($gnd);
                $links = $repository->getLinks($gnd, '_blank');
                include('templates/gndData.php');
                include('templates/beaconData.php');
            }
			else {
				echo '<p>Bitte &uuml;bergeben Sie eine GND-Nummer in der Form <a href="index.php?gnd=118559192">index.php?gnd=118559192</a></p>';
			}
            ?>
            </div>        
            <div class="row">
                <i>Ausgewertet wurden:</i> <?php echo $repository->showSources(); ?><br />
                <?php 
                    $failed = $repository->showSources(true); 
                    if ($failed) {
                        echo '<i>Nicht geladen:</i> '.$failed.'<br />'; 
                    } ?>
                <i>Letztes Update:</i> <?php echo date('Y-m-d H:i', $repository->lastUpdate); ?>
            </div>
            
        </div>
<?php include('templates/footer.php'); ?>
    </body>
</html>
