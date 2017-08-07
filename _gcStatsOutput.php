<?php
require ("classes/GcStatsRenderer.php");

function gcStats_shortcutHandler($atts)
{
    extract(shortcode_atts( array (
    'type'=>'total',
    'useflash'=>'no'
    ), $atts));

    switch($type)
    {
        case 'total':
            {
                return GcStatsRenderer::getCountFoundCaches($atts);
            }
        case 'container':
            {
                return GcStatsRenderer::getCachesByContainer($atts);
            }
        case 'type':
            {
                return GcStatsRenderer::getCachesByType($atts);
            }
        case 'matrix':
            {
                return GcStatsRenderer::getDiffTerrMatrix($atts);
            }
        case 'lastfounds':
            {
                return GcStatsRenderer::getLastFounds($atts);
            }
        case 'weekdays':
            {
                return GcStatsRenderer::getCachesByWeekday($atts);
            }
        case 'maxdist':
            {
                return GcStatsRenderer::getMaxDist($atts);
            }
        case 'countftf':
            {
                return GcStatsRenderer::getCountFTF($atts);
            }
    }
}
add_shortcode('GCSTATS', 'gcStats_shortcutHandler');

function gcStats__getInterfaceVersion(){
	return GCSTATS_OSM_INTERFACE_VERSION;
}

function gcStats__getNumOfCaches($accountame)
{
    return GcStatsRenderer::getCountFoundCaches($accountname);
}

// zum Auslesen eines Cashes (Idx=0...NumOfCashes-1)
// {lat : 49.56, lon : 8.11,
// type: unknown,
// text : 'Cache xyz, link <a href="...">Cache-Name</a>'}
function gcStats__getCacheData($accountname, $idx, $custom) {
	
}

// Gibt alle Cashes zurueck
// {lat : 49.56, lon : 8.11,
// type: unknown,
// text : 'Cache xyz, link <a href="...">Cache-Name</a>'},
// ...
function gcStats__getCachesData($accountname, $custom) {
	global $wpdb;
	
	$sql = "SELECT latitude AS lat, longitude AS lon, name, urlname, gsCacheType AS type FROM `".$wpdb->prefix."gcStats_waypoints`";
	if($accountname != ''){
		$sql .= " WHERE accountname = '".$accountname."'";
	}
	$sql .= ';';
	$result = $wpdb->get_results($sql);
	$out = array();
	foreach($result as $k => $v){
		$tmp = array(
			'lat' => $v->lat,
			'lon' => $v->lon,
			'type' => $v->type,
			'text' => '<h2>'.$v->name.'</h2><br />'.$v->urlname.'<br /><a href="http://coord.info/'.$v->name.'" target="_blank">Details...</a>'
		);
		array_push($out, $tmp);
	}
	return $out;
}

// GeoBereich dieses Users
// {min: -90, max: 90}
function gcStats__getMinMaxLat($accountname) {
	global $wpdb;
	
	$sql = "SELECT min_lat AS min, max_lat AS max FROM `".$wpdb->prefix."gcStats_options`";
	if($accountname != ''){
		$sql .= " WHERE accountname = '".$accountname."'";
	}
	$sql .= ';';
	$result = $wpdb->get_results($sql);
	return (array) $result[0];
}

// GeoBereich dieses Users
// {min: -180, max: 180}
function gcStats__getMinMaxLon($accountname) {
	global $wpdb;
	
	$sql = "SELECT min_lon AS min, max_lon AS max FROM `".$wpdb->prefix."gcStats_options`";
	if($accountname != ''){
		$sql .= " WHERE accountname = '".$accountname."'";
	}
	$sql .= ';';
	$result = $wpdb->get_results($sql);
	return (array) $result[0];
}

function gcStats_count_caches_by_gsCacheType()
{
    global $wpdb;

    $result = $wpdb->get_results("SELECT COUNT(*) AS cnt, gsCacheType AS type FROM `".$wpdb->prefix."gcStats_waypoints` GROUP BY gsCacheType;");
    return $result;
}

function gcStats_count_caches_by_gsContainer()
{
    global $wpdb;

    $result = $wpdb->get_results("SELECT COUNT(*) AS cnt, gsCacheContainer AS container FROM `".$wpdb->prefix."gcStats_waypoints` GROUP BY gsCacheContainer;");
    return $result;
}

function gcStats_getChallenge()
{
    global $wpdb;

    $result = $wpdb->get_results("
	SELECT COUNT(*) AS cnt, gsDifficulty, gsTerrain FROM `".$wpdb->prefix."gcStats_waypoints` 
	GROUP BY gsDifficulty, gsTerrain
	ORDER BY gsDifficulty, gsTerrain;
	");
    return $result;
}
?>