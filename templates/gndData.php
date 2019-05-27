<?php

$birth = false;
$death = false;
if ($gndRequest->dateBirth or $gndRequest->placeBirth) {
    $birth = true;
}
if ($gndRequest->dateDeath or $gndRequest->placeDeath) {
    $death = true;
}
$amBirth = 'am ';
$amDeath = 'am ';
if (strlen($gndRequest->dateBirth) == 4) {
    $amBirth = '';
}
if (strlen($gndRequest->dateDeath) == 4) {
    $amDeath = '';
}

?>
<h1>Personeninformation <small>(GND <?php echo $gnd; ?>)</small></h1>

<div class="well">
<?php if ($gndRequest->preferredName): ?>
<h2><?php if ($gndRequest->academicDegree) { echo $gndRequest->academicDegree.' '; } ?><?php echo displayType($gndRequest->type); ?><?php echo $gndRequest->preferredName; ?></h2>
<?php endif; ?>
<?php if ($birth == true): ?>
<p>Geboren <?php if ($gndRequest->dateBirth): ?> <?php echo $amBirth; ?><?php echo $gndRequest->dateBirth; ?><?php endif; ?><?php if ($gndRequest->placeBirth): ?> in <?php echo $gndRequest->placeBirth; ?><?php endif; ?>
<?php endif; ?>
<?php if ($birth == true and $death == true): ?>, gestorben
<?php elseif ($birth == false and $death == true): ?>Gestorben
<?php endif; ?>
<?php if ($death == true): ?>
<?php if ($gndRequest->dateDeath): ?> <?php echo $amBirth; ?><?php echo $gndRequest->dateDeath; ?><?php endif; ?><?php if ($gndRequest->placeDeath): ?> in <?php echo $gndRequest->placeDeath; ?><?php endif; ?></p>
<?php endif; ?>

<?php if ($gndRequest->info): ?>
<p><?php echo $gndRequest->info; ?></p>
<?php endif; ?>

<?php if (count($gndRequest->placesActivity) == 1): ?>
<p><i>Wirkungsort:</i> <?php echo $gndRequest->placesActivity[0]; ?></p>
<?php elseif (count($gndRequest->placesActivity) > 1): ?>
<p><i>Wirkungsorte:</i> <?php echo implode(', ', $gndRequest->placesActivity); ?></p>
<?php endif; ?>

<!--  <?php if (isset($gndRequest->variantNames[0])): ?>
<p><i>Weitere Namen:</i> <?php echo implode(', ', $gndRequest->variantNames); ?></p>
<?php endif; ?>
-->

<p><i>Quelle: </i><a href="http://d-nb.info/gnd/<?php echo $gnd; ?>" target="_blank" title="Link zu diesem Datensatz in der GND">GND</a></p>

</div>

