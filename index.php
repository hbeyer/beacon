<?php 
if (!empty($_GET['gnd'])) {
	header("Location: public/index.php?gnd=".$_GET['gnd']);
	exit();
}
header("Location: public/index.php");
?>