<?php

namespace HAB;

class RequestGND {

    public $gnd;
    public $errorMessage;
    private $base = 'http://hub.culturegraph.org/entityfacts/';
    private $response;
    
    public $preferredName;
    public $type;
    public $variantNames = array();
    public $info;
    public $dateBirth;
    public $dateDeath;
    public $placeBirth;
    public $placeDeath;
    public $placesActivity = array();
    public $academicDegree;
	public $relations = array();
	public $familialRelations = array();

    function __construct(gnd $gnd) {
        $this->gnd = $gnd;
        if ($this->gnd->valid == true) {
            $string = @file_get_contents($this->base.$this->gnd->id);
            if (!$string) {
                $this->errorMessage = 'Server hub.culturegraph.org/entityfacts/ antwortet nicht';
            }
            else {
                $this->response = json_decode($string);
                unset($string);
                foreach ($this->response as $key => $value) {
                    if ($key == 'preferredName') {
                        $this->preferredName = self::replaceUml($value);
                    }                    
                    if ($key == '@type') {
                        $this->type = $value;
                    }
                    if ($key == 'variantName') {
                        $this->variantNames = $value;
                    }                    
                    if ($key == 'biographicalOrHistoricalInformation') {
                        $this->info = self::replaceUml($value);
                    }
                    if ($key == 'dateOfBirth') {
                        $this->dateBirth = $value;
                    }
                    if ($key == 'dateOfDeath') {
                        $this->dateDeath = $value;
                    }
                    if ($key == 'placeOfBirth') {
                        $this->placeBirth = self::replaceUml($value[0]->preferredName);
                    }
                    if ($key == 'placeOfDeath') {
                        $this->placeDeath = self::replaceUml($value[0]->preferredName);
                    }
                    if ($key == 'placeOfActivity') {
                        foreach ($value as $place) {
                            $this->placesActivity[] = self::replaceUml($place->preferredName);
                        }
                    }
                    if ($key == 'academicDegree') {
                        $this->academicDegree = $value[0];
                    }
					if ($key == 'relatedPerson') {
						foreach ($value as $person) {
							$this->relations[] = self::getPersonArray($person);
						}
					}
					if ($key == 'familialRelationship') {
						foreach ($value as $person) {
							$this->familialRelations[] = self::getPersonArray($person);
						}
					}					
                }
                $this->response = null;
            }
        }
    }
	
	static function replaceUml($string) {
		$translate = array('Ä' => '&Auml;', 'Ö' => '&Ouml;', 'Ü' => '&Uuml;', 'ä' => '&auml;', 'ö' => '&ouml;', 'ü' => '&uuml;', 'ë' => '&euml;');
		$string = strtr($string, $translate);
		return($string);
	}
	
	static function getPersonArray($person) {
		$ret = array();
		foreach ($person as $key => $prop) {
			if (strstr($prop, 'https://d-nb.info/gnd') != null) {
				$ret['id'] = strtr($prop, array('https://d-nb.info/gnd/'  => ''));
			}
			else {
				$ret[$key] = self::replaceUml($prop);
			}
		}
		return($ret);
	}

}

?>
