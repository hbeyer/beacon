<?php

function makeNavigation() {
    $categories = array(
        'index.php' => array('label' => 'Personeninformation', 'active' =>  '')
    );

    $chunks = explode('/', $_SERVER['SCRIPT_FILENAME']);
    $current = array_pop($chunks);
    if (isset($categories[$current])) {
        $categories[$current]['active'] = ' class="active"';
    }
    include('navigation.php');
}

// Die Funktion ersetzt kombinierende diakritische Zeichen (hier nicht als solche erkennbar) durch HMLT-Entities, um die versetzte Darstellung der Punkte in Firefox zu beheben.
function replaceUml($string) {
	$translate = array('Ä' => '&Auml;', 'Ö' => '&Ouml;', 'Ü' => '&Uuml;', 'ä' => '&auml;', 'ö' => '&ouml;', 'ü' => '&uuml;', 'ë' => '&euml;');
	$string = strtr($string, $translate);
	return($string);
}

?>
