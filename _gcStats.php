<?php
define(GCSTATS_PLUGIN_FILE, dirname(__FILE__) . '/gcStats.php');
define(GCSTATS_UPLOAD_FOLDER, dirname(__FILE__) . '/upload/');
define(GCSTATS_PLUGIN_FOLDER, dirname(__FILE__) . '/');

require("classes/GpxDocument.php");
require("classes/WayPoint.php");
require("_gcStatsOutput.php");
require("_gcStatsWidget.php");

wp_enqueue_script('swfobject','/wp-content/plugins/gcstats/ofc/swfobject.js');

function gcStats_init(){
	global $wpdb;
	
	if(isset($_GET['activate']) || isset($_GET['activate-multi'])) {
		gcStats_activate();
	}
}

function gcStats_activate() {
	global $wpdb;
	
	gcStats_install();
}

function gcStats_deactivate() {
	global $wpdb;
	//TODO: Delete DB-Tables	
	gcStats_safe_query("DROP TABLE IF EXISTS `".$wpdb->prefix."gcStats_waypoints`");
	gcStats_safe_query("DROP TABLE IF EXISTS `".$wpdb->prefix."gcStats_options`");
	gcStats_safe_query("DROP TABLE IF EXISTS `".$wpdb->prefix."gcStats_error_log`");
	delete_option("gcstats_db_version_".$wpdb->prefix."gcStats_waypoints");
	delete_option("gcstats_db_version_".$wpdb->prefix."gcStats_options");
	delete_option("gcstats_db_version_".$wpdb->prefix."gcStats_error_log");
}

function gcStats_install_table($sql, $tablename){
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	if($wpdb->get_var("show tables like '".$tablename."';" != $tablename)) {
		dbDelta($sql);
		add_option("gcstats_db_version_".$tablename, GCSTATS_DB_VERSION);
	}
	$installed_version = get_option("gcstats_db_version_".$tablename);
	if($installed_version != GCSTATS_DB_VERSION) {
		dbDelta($sql);
		update_option("gcstats_db_version_".$tablename, GCSTATS_DB_VERSION);
	}
}

function gcStats_install() {
	global $wpdb;
	
	$sql = "CREATE TABLE ".$wpdb->prefix."gcStats_error_log (
	  id int(11) NOT NULL auto_increment,
	  message varchar(255) NOT NULL,
	  timestamp int(11) NOT NULL,
	  PRIMARY KEY  (id)
	);";
	gcStats_install_table($sql, $wpdb->prefix."gcStats_error_log");
	
	$sql = "CREATE TABLE ".$wpdb->prefix."gcStats_waypoints (
	  id int(11) NOT NULL auto_increment,
	  name varchar(250) NOT NULL,
	  description text,
	  time varchar(250),
	  url varchar(250),
	  urlname varchar(250),
	  sym varchar(250),
	  gsCacheId varchar(250),
	  gsPlacedBy varchar(250),
	  gsCacheType varchar(50),
	  gsCacheContainer varchar(50),
	  gsDifficulty varchar(5),
	  gsTerrain varchar(5),
	  country varchar(250),
	  state varchar(250),
	  latitude varchar(250),
	  longitude varchar(250),
	  ftf varchar(1),
	  accountname varchar(250),
	  newSinceLastUpload varchar(1),
	  CJRatingAVG varchar(250),
	  CJDetailLink varchar(250),
	  PRIMARY KEY  (id)
	);";
	gcStats_install_table($sql, $wpdb->prefix."gcStats_waypoints");
	
	$sql = "CREATE TABLE ".$wpdb->prefix."gcStats_options (
	  id int(11) NOT NULL auto_increment,
	  last_import int(11),
	  last_gpx_date varchar(50),
	  min_lat varchar(50),
	  min_lon varchar(50),
	  max_lat varchar(50),
	  max_lon varchar(50),
	  accountname varchar(250) UNIQUE,
	  default_account varchar(1),
	  PRIMARY KEY (id)
	) ;";
	gcStats_install_table($sql, $wpdb->prefix."gcStats_options");
}

class GcStats_Error extends Exception {}

function gcStats_safe_query($sql) {
	global $wpdb;
	$result = $wpdb->query($sql);
	if($result === false){
		if($wpdb->error){
			$reason = $wpdb->error->get_error_message();
		} else {
			$reason = __('Unknown SQL Error', 'gcStats');
		}
		gcStats_log_error($reason);
		//throw new GcStats_Error($reason);
	}
	return $result;
}

function gcStats_log_error($message){
	global $wpdb;
	
	$result = $wpdb->query(sprintf("INSERT INTO `".$wpdb->prefix."gcStats_error_log` (`message`, `timestamp`) VALUES ('%s','%d')", $wpdb->escape($message), time()));
}

function gcStats_toggleFTF(){
	global $wpdb;
	
	$result = $wpdb->get_results("SELECT ftf FROM `".$wpdb->prefix."gcStats_waypoints` WHERE name='".$_POST['wp']."'; ");
	if($result[0]->ftf == '1'){
		$ftfval = '0';
	} else {
		$ftfval = '1';
	}
	$result = $wpdb->get_results("UPDATE `".$wpdb->prefix."gcStats_waypoints` SET `ftf`='".$ftfval."' WHERE name='".$_POST['wp']."';");
	die( $ftfval);
}
add_action('wp_ajax_gcStats_toggleFTF', 'gcStats_toggleFTF');

function gcStats_admin_menu_upload_page(){
	global $gpxDoc;
	include("pages/page_upload.php");
}

function gcStats_admin_menu_error_log(){
	global $wpdb;
	include "pages/page_errorlog.php";
}

function makeDiffTerrStars(){
	
}

function gcStats_admin_menu_all_stats(){
	global $wpdb;
	
	//echo '<script src="http://localhost/wordpress/wp-content/plugins/gcStats/ofc/js/swfobject.js"></script>';
	//echo '<div class="wrap"><h2>GeoCaching Statistiks</h2>';
	//echo getCachesByContainerAsFlashChart();
	//echo '<table><tbody>';
	
	$total = gcStats_count_found_caches();
	echo '<tr><td>Total found Caches:</td><td>'.$total.'</td></tr>';
	
	$byType = gcStats_count_caches_by_gsCacheType();
	foreach ($byType as $key => $value) {
		$pct = 100 / $total * $value->cnt;
		echo '<tr><td>Found '.$value->type.'s:</td><td>'.$value->cnt.' ('.round($pct,1).'%)</td></tr>';
	}
	
	$byContainer = gcStats_count_caches_by_gsContainer();
	foreach ($byContainer as $key => $value) {
		$pct = 100 / $total * $value->cnt;
		echo '<tr><td>Found '.$value->container.'s:</td><td>'.$value->cnt.' ('.round($pct,1).'%)</td></tr>';
	}
	
	echo '<tr><td>Challenge:</td><td><table><thead><tr><th>Difficulty</th><th>Terrain</th><th>Found</th></tr></thead><tbody>';
	$challengeData = gcStats_getChallenge();
	$challengeArray = array();
	for($i = 1; $i <= 5; $i+=0.5){
		for($j = 1; $j <= 5; $j+=0.5){
			$challengeArray[(string) $i][(string) $j] = 'not found yet' ;
		}
	}
	foreach($challengeData as $key => $value){
		$challengeArray[$value->gsDifficulty][$value->gsTerrain] = $value->cnt;
	}
	
	for($i = 1; $i <= 5; $i+=0.5){
		for($j = 1; $j <= 5; $j+=0.5){
			echo '<tr><td>'.$i.'</td><td>'.$j.'</td><td>'.$challengeArray[(string) $i][(string) $j].'</td></tr>';
		}
	}
	//	echo '<tr><td>'.$value->gsDifficulty.'</td><td>'.$value->gsTerrain.'</td><td>'.$value->cnt.'</td></tr>';
	//}
	echo '</tbody></table></td></tr>';
	echo '</tbody></table>';
	echo '</div>';
}

function gcStats_admin_menu_options(){
	global $wpdb;
	
	$sql = 'SELECT accountname, default_account FROM '.$wpdb->prefix.'gcStats_options;';
	$accountnames = $wpdb->get_results($sql);
	if(isset($_POST["gcstats_option_page"]) && $_POST["gcstats_option_page"] == "general"){
		if(isset($_POST["action"]) && $_POST["action"] == "update"){
			$sql1 = 'UPDATE '.$wpdb->prefix.'gcStats_options SET default_account = "";';
			$sql2 = 'UPDATE '.$wpdb->prefix.'gcStats_options SET default_account = "1" WHERE accountname = "'.$_POST['dflt_acc_name'].'";';
			$wpdb->query($sql1);
			$wpdb->query($sql2);
			$msg_updated = "Options saved!";
		}
	}
	include "pages/page_options.php";
}

function gcStats_admin_menu_edit(){
	global $wpdb;
	include "pages/page_edit.php";
}

function gcStats_admin_menu_add_pages(){
	add_menu_page('gcStats', 'gcStats', 8, __FILE__, 'gcStats_admin_menu_upload_page');
	add_submenu_page(__FILE__, 'Upload', 'Upload', 8, __FILE__, 'gcStats_admin_menu_upload_page');
	add_submenu_page(__FILE__, 'Edit', 'Edit', 8, 'gcStats_edit', 'gcStats_admin_menu_edit');
	add_submenu_page(__FILE__, 'Options', 'Options', 8, 'gcStats_options', 'gcStats_admin_menu_options');
	//add_submenu_page(__FILE__, 'AllStatistiks', 'AllStatistiks', 8, 'allStats', 'gcStats_admin_menu_all_stats');
	//add_submenu_page(__FILE__, 'ErrorLog', 'ErrorLog', 8, 'errorLog', 'gcStats_admin_menu_error_log');
}
add_action('admin_menu','gcStats_admin_menu_add_pages');

add_action('init','gcStats_init');
register_deactivation_hook(GCSTATS_PLUGIN_FILE, 'gcStats_deactivate');
if(isset($_FILES['gpxfile']) && isset($_POST) && $_GET['page']=='gcstats/_gcStats.php'){
	$filename = GCSTATS_UPLOAD_FOLDER.$_FILES['gpxfile']['name'];
	move_uploaded_file($_FILES['gpxfile']['tmp_name'], $filename);
	global $gpxDoc;
	$gpxDoc = new GpxDocument($filename);
	//$result = gcStats_safe_query("DELETE FROM `".$wpdb->prefix."gcStats_waypoints` WHERE accountname='".$wpdb->escape($gpxDoc->getAccountname())."'");
	$gpxDoc->save();
}

function gcStats_getCronCJRating(){
	global $wpdb;
	
	$result = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."gcStats_waypoints` ;");
	
	foreach ($result as $key=>$value)
    {
        $res = wp_remote_request('http://www.cachejudge.com/index.php/api/read/?call=read_cache&waypoint='.$value->name);
		$xml = simplexml_load_string($res['body']);
		if($xml->Cache){
			$cjAVGRating = $xml->Cache->cjAVGRating->attributes()->Average;
			$cjLink = $xml->Cache->Link;
		} else {
			$cjAVGRating = '';
			$cjLink = '';
		}
		$wpdb->update($wpdb->prefix."gcStats_waypoints", array('CJRatingAVG' => $cjAVGRating, 'CJDetailLink' => $cjLink),array('id' => $value->id),array('%s','%s'),array('%d'));
    }
}
//wp_schedule_event(time(), 'hourly', 'gcStats_getCronCJRating');
//gcStats_getCronCJRating();
?>
