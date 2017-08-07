=== gcStats ===
Tags: geo, GPX, geocache, geocaching, OSM, statistic
Requires at least: 2.7.0
Tested up to: 2.7.1
Stable tag: 0.2.2

gcStats plugin to embed some statistics with found geocaches in your blog. 

== Description ==

gcStats is a plugin that focuses on GeoCaching and displays some Statistics in your blog.

* NEW! Display your geocaches using the osm plugin by Michael Kang on a map
* using shortcodes to add Statistics to your Blog
* add a widget to your sidebar which displays types of caches you have found
* Use Flash or HTML to visualize your Statistics
* Simply upload your myFinds-GPX-File from geocaching.com and insert shortcodes 

== Installation ==

1. Upload gcStats folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Get your "MyFinds"-Pocketquery from geocaching.com, extract it and upload the (your user-id).gpx file in gcStats upload form
3. Add shortcodes to a page (see http://michael.josi.de/projects/gcstats/ for some examples)

== Frequently Asked Questions ==

= How can I display my Finds on a map ? =

You have to install [osm plugin](http://www.Fotomobil.at/wp-osm-plugin "Link to osm plugin") to use display a map with your caches.
Then you need a shortcode like this: [ osm_map import="gcstats,gcAccountName" ]

== Screenshots ==

No Screenshots taken yet.

== Shortcodes ==

All shortcodes will look like this: [ GCSTATS type="***" ]
Possible values for attribute type:
* total
* countftf
* lastfounds (additional attribute "number" for "My last number founds)
* maxdist (needs attributes "lat" and "lon" for home-coordinates)
* container (possible attribute useflash="yes")
* weekdays (possible attribute useflash="yes")
* type (possible attribute useflash="yes")
* matrix

To display the OSM-Map please install the [osm plugin](http://www.Fotomobil.at/wp-osm-plugin "Link to osm plugin") 
and add the OSM-shortcode [ osm_map import="gcstats,gcAccountName" ]

Please also see the examples on http://michael.josi.de/projects/gcstats/
