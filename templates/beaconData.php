<?php if (isset($links[0])): ?>
<h2>Ressourcen zu dieser Person</h2>
<ul>
<?php foreach ($links as $link): ?>
<li><?php echo $link; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<hr />
