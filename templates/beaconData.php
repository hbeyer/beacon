<?php if (isset($linksHAB[0])): ?>
<h2>Ressourcen der HAB</h2>
<ul>
<?php foreach ($linksHAB as $link): ?>
<li><?php echo $link; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if (isset($otherLinks[0])): ?>
<h2>Externe Ressourcen</h2>
<ul>
<?php foreach ($otherLinks as $link): ?>
<li><?php echo $link; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<hr />

