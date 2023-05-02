<?php

// replaceUml nach RequestGND migrieren

namespace HAB;

class FunctionLib {

	// Die Funktion ersetzt kombinierende diakritische Zeichen (hier nicht als solche erkennbar) durch HMLT-Entities, um die versetzte Darstellung der Punkte in Firefox zu beheben.
	static function replaceUml($string) {
		$translate = array('Ä' => '&Auml;', 'Ö' => '&Ouml;', 'Ü' => '&Uuml;', 'ä' => '&auml;', 'ö' => '&ouml;', 'ü' => '&uuml;', 'ë' => '&euml;');
		$string = strtr($string, $translate);
		return($string);
	}

}

?>
