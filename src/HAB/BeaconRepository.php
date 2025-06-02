<?php

namespace HAB;

class BeaconRepository {

    public $errorMessages = array();
    public $lastUpdate;
    public $valid = false;
    public $folder = '../data/beaconFiles';
    public $beacon_sources;
    private $update_int = 1209600;
    private $filePermission = 0777;
    private $user = 'Herzog August Bibliothek Wolfenbüttel';
    private $sourcesHAB = array('bahnsen', 'fruchtbringer', 'cph', 'aqhab', 'vkk', 'porthab', 'sandrart', 'hainhofer', 'hainsb', 'duennh', 'tc2a'); // Hier wird festgelegt, welche der unten stehenden Quellen als "Ressourcen der HAB" angezeigt werden sollen

    function __construct($update = true, $folder = null) {
		if ($folder != null) {
			$this->folder = $folder;
		}
		$parser = new \Symfony\Component\Yaml\Parser();
		$sources = $parser->parse(file_get_contents($this->folder.'/../sources.yml'));
		$this->beacon_sources = $sources['sources']['all'];
		$this->valid = $this->validate();
        $dateArchive = intval(file_get_contents($this->folder.'/changeDate'));
        $this->lastUpdate = date('d.m.Y', $dateArchive);		
        if ($update == false) {
            return;
        }
        if ($this->valid == false) {
            if (!is_dir($this->folder)) {
                mkdir($this->folder, 0777);
            }
            $this->update();            
			return;
		}
        if ((date('U') - $dateArchive) > $this->update_int) {
                $this->update();
        }
		return;
    }

	/*
    private function secondsSinceLastUpdate() {
        return(date('U') - $this->lastUpdate);
    }
	*/

    public function update() {
        echo "Aktualisieren der BEACON-Dateien unter beaconFiles\n";
		$arrContextOptions = array(
			'ssl' => array(
				'cafile' => '../certs/collection.pem',
				'verify_peer'=> true,
				'verify_peer_name'=> true
				),
			'http' => array(
				'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
			)	
		);
		foreach ($this->beacon_sources as $key => $source) {
			$response = @file_get_contents($source['location'], false, stream_context_create($arrContextOptions));
			if (is_string($response) == false) {
				echo 'Fehler beim Download von '.$source['location'].' nach '.$key."\n";
				continue;
			}
			file_put_contents($this->folder.'/'.$key, $response);
			//chmod($this->folder.'/'.$key, $this->filePermission);
		}
        $date = date('U');
        file_put_contents($this->folder.'/changeDate', $date);
        //chmod($this->folder.'/changeDate', $this->filePermission);
        $this->lastUpdate = $date;
    }

    public function getLinks(gnd $gnd, $target = '') {
        if ($gnd->valid != true) {
            return(null);
        }
        $result = array();
        $matches = $this->getMatches($gnd->id);
        foreach ($matches as $key) {
			$result[] = $this->makeLink($key, $gnd->id, $target);
        }
        return($result);
    }

    public function getSelectedLinks(gnd $gnd, $hab = true, $target = '') {
        if ($gnd->valid != true) {
            return(null);
        }
        $result = array();
        $matches = $this->getMatches($gnd->id);
        foreach ($matches as $key) {
            if (in_array($key, $this->sourcesHAB) and $hab == false) {
                continue;
            }
            elseif (!in_array($key, $this->sourcesHAB) and $hab == true) {
                continue;
            }
			$result[] = $this->makeLink($key, $gnd->id, $target);
        }
        return($result);
    }

    public function getLinksMulti($gndArray, $target = '') {
        $result = array();
        $matches = $this->getMatchesMulti($gndArray);
        foreach ($matches as $gnd => $keys) {
            $resultPart = array();
            foreach ($keys as $key) {
                $resultPart[] = $this->makeLink($key, $gnd, $target);
            }
            $result[$gnd] = $resultPart;
        }
        return($result);
    }

    /*
	function showSources($showFailed = false, $sep = '; ') {
        $success = array();
        $failed = array();
        foreach ($this->beacon_sources as $key => $source) {
            if (file_exists($this->folder.'/'.$key)) {
                $success[] = $source['label'];
            }
            else {
                $failed[] = $source['label'];
            }
        }
        if ($showFailed == true) {
            return(implode($sep, $failed));
        }
        return(implode($sep, $success));
    }
	*/

    private function getMatches($gnd) {
        $result = array();
        foreach ($this->beacon_sources as $key => $source) {
            $content = file_get_contents($this->folder.'/'.$key);
            if (strpos($content, $gnd) != null) {
                $result[] = $key;
            }
        }
        return($result);
    }

    private function getMatchesMulti($gndArray) {
        $result = array();
        foreach($this->beacon_sources as $key => $source) {
            $content = file_get_contents($this->folder.'/'.$key);
            foreach ($gndArray as $gnd) {
                if (strpos($content, $gnd) != null) {
                $result[$gnd][] = $key;
                }
            }
        }
        return($result);
    }

    private function makeLink($key, $gnd, $target) {
        if (in_array($target, array('_blank', '_self', '_parent', '_top'))) {
            $target = ' target="'.$target.'"';
        }
        if ($this->beacon_sources[$key]['type'] == 'specified') {
            $url = $this->extractURL($gnd, $key);
        }
        else {
            $url = strtr($this->beacon_sources[$key]['target'], array('{ID}' => $gnd));
        }
        $link  = '<a href="'.$url.'"'.$target.'>'.$this->beacon_sources[$key]['label'].'</a>';
        return($link);
    }

    private function extractURL($gnd, $key) {
        $string = file_get_contents($this->folder.'/'.$key);
        preg_match('~'.$gnd.'\|([^|]+)?\|([^|]+)?\s~', $string, $hits);
        if (!empty($hits[2])) {
            if (substr($hits[2], 0, 4) == 'http') {
                return($hits[2]);
            }
            elseif (!empty($this->beacon_sources[$key]['target'])) {
                $target = $this->beacon_sources[$key]['target'];
                $url = strtr($target, array('{ID}' => $hits[2]));
                return($url);
            }
        }
        return('');
    }

	public function getTypeArray() {
		$res = array();
		foreach ($this->beacon_sources as $key => $src) {
			$src["id"] = $key;
			if (isset($res[$src["dbtype"]])) {
				$res[$src["dbtype"]][] =  $src;
			}
			else {
				$res[$src["dbtype"]] = array($src);
			}
		}
		return($res);
	}	

    private function validate() {
        if (!is_dir($this->folder)) {
			$this->errorMessages[] = 'Ordner existiert nicht';
            return(false);
        }
        if (!file_exists($this->folder.'/changeDate')) {
			$this->errorMessages[] = 'changeDate existiert nicht';
            return(false);
        }
        $this->lastUpdate = intval(file_get_contents($this->folder.'/changeDate'));
        if ($this->lastUpdate < 1400000000 or $this->lastUpdate > date('U')) {
			$this->errorMessages[] = 'changeDate ist nicht plausibel';
            return(false);
        }
        foreach ($this->beacon_sources as $key => $source) {
            if (!file_exists($this->folder.'/'.$key)) {
				$this->errorMessages[] = 'Beacon Datei für '.$this->beacon_sources[$key]['label'].' nicht lokal vorhanden';
                return(false);
            }
        }
        return(true);
    }

}

?>
