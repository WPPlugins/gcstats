<?php

class GpxDocument
{

    /**
     * Returns $accountname.
     * @see GpxDocument::$accountname
     */
    public function getAccountname()
    {
        return $this->accountname;
    }
    
    /**
     * Sets $accountname.
     * @param object $accountname
     * @see GpxDocument::$accountname
     */
    public function setAccountname($accountname)
    {
        $this->accountname = (string) $accountname;
    }

    /**
     * Returns $maxlat.
     * @see GpxDocument::$maxlat
     */
    public function getMaxlat()
    {
        return $this->maxlat;
    }
    
    /**
     * Sets $maxlat.
     * @param object $maxlat
     * @see GpxDocument::$maxlat
     */
    public function setMaxlat($maxlat)
    {
        $this->maxlat = $maxlat;
    }
    
    /**
     * Returns $maxlon.
     * @see GpxDocument::$maxlon
     */
    public function getMaxlon()
    {
        return $this->maxlon;
    }
    
    /**
     * Sets $maxlon.
     * @param object $maxlon
     * @see GpxDocument::$maxlon
     */
    public function setMaxlon($maxlon)
    {
        $this->maxlon = $maxlon;
    }
    
    /**
     * Returns $minlat.
     * @see GpxDocument::$minlat
     */
    public function getMinlat()
    {
        return $this->minlat;
    }
    
    /**
     * Sets $minlat.
     * @param object $minlat
     * @see GpxDocument::$minlat
     */
    public function setMinlat($minlat)
    {
        $this->minlat = $minlat;
    }
    
    /**
     * Returns $minlon.
     * @see GpxDocument::$minlon
     */
    public function getMinlon()
    {
        return $this->minlon;
    }
    
    /**
     * Sets $minlon.
     * @param object $minlon
     * @see GpxDocument::$minlon
     */
    public function setMinlon($minlon)
    {
        $this->minlon = $minlon;
    }

    /**
     * Returns $author.
     * @see GpxDocument::$author
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Sets $author.
     * @param object $author
     * @see GpxDocument::$author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
    /**
     * Returns $bounds.
     * @see GpxDocument::$bounds
     */
    public function getBounds()
    {
        return $this->bounds;
    }
    
    /**
     * Sets $bounds.
     * @param object $bounds
     * @see GpxDocument::$bounds
     */
    public function setBounds($bounds)
    {
        $this->bounds = $bounds;
    }
    
    /**
     * Returns $desc.
     * @see GpxDocument::$desc
     */
    public function getDesc()
    {
        return $this->desc;
    }
    
    /**
     * Sets $desc.
     * @param object $desc
     * @see GpxDocument::$desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }
    
    /**
     * Returns $email.
     * @see GpxDocument::$email
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Sets $email.
     * @param object $email
     * @see GpxDocument::$email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * Returns $keywords.
     * @see GpxDocument::$keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    /**
     * Sets $keywords.
     * @param object $keywords
     * @see GpxDocument::$keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }
    
    /**
     * Returns $name.
     * @see GpxDocument::$name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets $name.
     * @param object $name
     * @see GpxDocument::$name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Returns $time.
     * @see GpxDocument::$time
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * Sets $time.
     * @param object $time
     * @see GpxDocument::$time
     */
    public function setTime($time)
    {
        $this->time = (string) $time;
    }

	private $name;
	
	private $desc;
	
	private $author;
	
	private $email;
	
	private $time;
	
	private $keywords;
	
	private $bounds;
	
	private $countCachesFound = 0;
	
	private $arrCacheTypes = array();
	
	private $arrWayPoints = array();
	
	private $minlat = "";
	
	private $minlon = "";
	
	private $maxlat = "";
	
	private $maxlon = "";
	
	private $accountname = "";
	
	public function addWayPoint($wpt){
		if($wpt->sym == "Geocache Found"){
			$this->countCachesFound++;
			$this->arrCacheTypes["".$wpt->type] ++;
		}
		$tmpWayPoint = new WayPoint();
		$tmpWayPoint->setDesc($wpt->desc);
		$tmpWayPoint->setName($wpt->name);
		$tmpWayPoint->setSym($wpt->sym);
		$tmpWayPoint->setUrl($wpt->url);
		$tmpWayPoint->setUrlname($wpt->urlname);
		$tmpWayPoint->setLatitude($wpt->attributes()->lat);
		$tmpWayPoint->setLongitude($wpt->attributes()->lon);
		$gsWpt = $wpt->children("http://www.groundspeak.com/cache/1/0");
		$tmpWayPoint->setTime($gsWpt->cache->logs->log->date);
		$tmpWayPoint->setGsCacheType($gsWpt->cache->type);
		$tmpWayPoint->setState($gsWpt->cache->state);
		$tmpWayPoint->setGsCacheContainer($gsWpt->cache->container);
		$tmpWayPoint->setGsDifficulty($gsWpt->cache->difficulty);
		$tmpWayPoint->setGsTerrain($gsWpt->cache->terrain);
		$tmpWayPoint->setCountry($gsWpt->cache->country);
		$tmpWayPoint->setAccountname($gsWpt->cache->logs->log->finder);
		$this->setAccountname($gsWpt->cache->logs->log->finder);
		array_push($this->arrWayPoints, $tmpWayPoint);
	}
	
	public function countWayPoints(){
		return sizeof($this->arrWayPoints);
	}
	
	public function __construct($pathToGpx){
		$xml = simplexml_load_file($pathToGpx);
		$this->setName($xml->name);
		$this->setDesc($xml->desc);
		$this->setTime($xml->time);
		$this->setMinlat($xml->bounds->attributes()->minlat);
		$this->setMinlon($xml->bounds->attributes()->minlon);
		$this->setMaxlat($xml->bounds->attributes()->maxlat);
		$this->setMaxlon($xml->bounds->attributes()->maxlon);
		foreach($xml->wpt as $wpt){
			$this->addWayPoint($wpt);
		}
	}
	
	public function save(){
		global $wpdb;
		$result = $wpdb->get_results("
		SELECT COUNT(*) AS cnt 
		FROM `".$wpdb->prefix."gcStats_options 
		WHERE accountname='".$wpdb->escape($this->getAccountname())."';
		");
		if($result[0]->cnt!=0){
			$result = gcStats_safe_query("
			UPDATE `".$wpdb->prefix."gcStats_options` 
			SET 
			`last_import`=".time().",
			`min_lat`='".$wpdb->escape($this->getMinlat())."',
			`min_lon`='".$wpdb->escape($this->getMinlon())."',
			`max_lat`='".$wpdb->escape($this->getMaxlat())."',
			`max_lon`='".$wpdb->escape($this->getMaxlon())."',
			`last_gpx_date`='".$wpdb->escape($this->getTime())."'
			WHERE 
			`accountname`='".$wpdb->escape($this->getAccountname())."'; 
			");
		} else {
			$result = gcStats_safe_query("
			INSERT INTO `".$wpdb->prefix."gcStats_options`
			(`last_import`,`min_lat`,`min_lon`,`max_lat`,`max_lon`,`accountname`,`last_gpx_date`) 
			VALUES (".time().",'".$wpdb->escape($this->getMinlat())."','".$wpdb->escape($this->getMinlon())."',
			'".$wpdb->escape($this->getMaxlat())."','".$wpdb->escape($this->getMaxlon())."',
			'".$wpdb->escape($this->getAccountname())."','".$wpdb->escape($this->getTime())."');
			");
		}
		foreach($this->arrWayPoints as $wpt){
			if($wpt->save()){
				$this->newRecords++;
			};
		}
	}
	
	public $newRecords = 0;
	
	public function toHTML(){
		print_r($this->arrWayPoints);
		foreach ($this->arrCacheTypes as $type => $value) {
			$html .= $type . ": " . $value . "<br />";
		}
		$html .= "Total: " . $this->countCachesFound . "<br />";
		return $html;
	}
}

?>