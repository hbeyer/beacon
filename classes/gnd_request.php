<?php

class gnd_request {

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
                        $this->preferredName = replaceUml($value);
                    }                    
                    if ($key == '@type') {
                        $this->type = $value;
                    }
                    if ($key == 'variantName') {
                        $this->variantNames = $value;
                    }                    
                    if ($key == 'biographicalOrHistoricalInformation') {
                        $this->info = replaceUml($value);
                    }
                    if ($key == 'dateOfBirth') {
                        $this->dateBirth = $value;
                    }
                    if ($key == 'dateOfDeath') {
                        $this->dateDeath = $value;
                    }
                    if ($key == 'placeOfBirth') {
                        $this->placeBirth = replaceUml($value[0]->preferredName);
                    }
                    if ($key == 'placeOfDeath') {
                        $this->placeDeath = replaceUml($value[0]->preferredName);
                    }
                    if ($key == 'placeOfActivity') {
                        foreach ($value as $place) {
                            $this->placesActivity[] = replaceUml($place->preferredName);
                        }
                    }
                    if ($key == 'academicDegree') {
                        $this->academicDegree = $value[0];
                    }                                  
                }
                $this->response = null;
            }
        }
    }

}

?>
