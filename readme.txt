=== WP Design Maps & Places ===
Contributors: alexander.herdt
Donate link: http://amazingweb.de/donation/
Tags: google, google map, google maps, google places, designer map, map, map markers, maps, wp google map, wp google maps, wp maps
Requires at least: 3.6
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin allows you to mark places on a CUSTOM MAP coherent with the style of your website, if you are not happy with Google Map's look.

== Description ==
Visualise your offices, locations, project sites etc. in the  design of your website! Integrate your self-designed map and marker images into your website and easily add new locations by just entering the address or geographic coordinates. The plugin uses the comfortable geolocation feature from Google Geocoder (Google Maps).

The plugin allows for multiple maps (Mapimages) to be integrated on different pages. It also plugin also supports multilanguage sites.

To wrap up - the WP Design Maps & Places is:
    * Design friendly. Design your own map and link it to geographic coordinates.
    * Easy to manage. The site administrator can add and publish new locations in seconds without having to involve a designer.
    * Lightweight. The map loads quickly.
    * Visitor friendly. A self-designed map allows you to fade out distracting information.        

== Installation ==

1. Unzip the package and upload its contents into the '/wp-content/plugins/' directory or directly upload the zip package via WordPress default plugin installer.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Maps & Places - Settings page and define the languages you need. 
4. Go to Maps & Places - Maps and 
    a. upload you map and markers (or use the default available after installation)
    b. connect the map to the geolocation by overlay on Google Map or defining two reference points.
5. Go to Maps & Places and mark you places
6. Add a short code [wpdmp map="x" lang="xyz"] to the target page.
That's it!

== Frequently Asked Questions ==

* What should be considered for map images?
    - the images can have wrong height/width proportion, but proportions on one axis should be the same. You can use maps of cities, countries or parts of them. 
 
* How my map can be connected to geolocation?
    - there are two ways to bound your map to geocoordinates – “Calibrate via Google Map” (the link in “Map Manager”) or by definition of two reference points.
   
* What is a reference point?
    - a reference point is like a pin which binds your map to the real geographic map. In order to calculate the position of a location on the map image two reference points are needed.
    
* How do I place a reference point?
    - you need to have an about a place (part of address) on your map. If the image itself does not contain any places you can do the positioning by using other markings (borders, rivers, mountains etc). After you find a point which can be used as the reference point on the map, go to "Maps" and do click on this point. Enter the address of this point into the field in the newly opening dialogue. The address may contain country, post code, city, street -- Google will find it. However, we recommend to always enter the country as part of the address as some city names occur more than once (e.g. "Naples (Italy) and Naples (Florida, USA).
    
* How do I upload a map or marker images?
    - Go to "Maps Manager" and click "Add Map". In the "Media Library" dialogue you can either upload a new map image or reuse an already existing one. To define the marker images for every map, click “Add / Remove Markers”.

* How many maps can I place on one page?
    - the current version allows for only one map per page.
    
* How do I define my own markers?
    - you can upload the images of the markers into the <WP plugin directory>/images/markers directory. The images must have the extensions "png", "jpg", "gif" or "jpeg".

* How can I apply own styles or effects on markers and Place Popups?
	-	In “Settings” you can find the CSS and Effects edit fields. Put your css or JavaScript there. The content of the fields will be inserted to your front-, and backend HTML.
	Example of the CSS selector which can be used for marker images for Map with id=2:
		#mapoverlay[mapid='2']>.ctrl
	The popups can be addressed by:
		#mapoverlay[mapid='2']>. mappopupwrap
		
== Screenshots ==

1. Design your individual Map
2. Upload and calibrate your Map to fit Google Maps
3. Add your Places by simply typing their addresses
4. Put the shortcut on a post or page and enjoy your Map your website!

== Changelog ==
first release

== Upgrade Notice ==
just install

== Arbitrary section ==