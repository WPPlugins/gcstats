<?php
/*
 Plugin Name: gcStats
 Plugin URI: http://michael.josi.de/projects/gcStats
 Description: Geocaching Statistiks - import your MyFinds.gpx-file and generate some statistiks of your found caches
 Author: Michael Jostmeyer
 Version: 0.2.2
 Author URI: http://michael.josi.de
 Minimum WordPress Version Required: 2.7.0
 */

/*  (c) Copyright 2009  Michael Jostmeyer (michael@josi.de)
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* 
 Keep in mind, all changes you do by your own are lost
 whenever you update your plugin. If you need any general
 feature contact me to make a standard of gcStats plugin!
 */

if (phpversion() >= 5)
{
    define(GCSTATS_VERSION, '0.2.2');
    define(GCSTATS_PLUGIN_FILE, __FILE__ );
    define(GCSTATS_OSM_INTERFACE_VERSION, 1);
	define(GCSTATS_DB_VERSION, 2);
    global $wp_version;
    if (version_compare($wp_version, "2.7.0", "<")) {
        exit ('[gcStats plugin - ERROR]: At least Wordpress Version 2.7.0 is needed for this plugin!');
    }
    include ('_gcStats.php');
} else {
    exit ('[gcStats plugin - ERROR]: gcStats won\'t work under PHP 4. Please upgrade to PHP 5 and try again!');
}

?>
