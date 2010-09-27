=== Simple coverflow ===
Contributors: Simon hansen
Donate link:   none
Tags: gallery, lightbox, images, jquery,thickbox
Requires at least: 3.0
Tested up to: 3.0
Stable tag: Trunk


A very simple coverflow plugin. For php programmers easy to modiffy

== Description ==   

A very simple coverflow plugin. For php programmers easy to modiffy
insert shortag [coverflow] in your post and the plugin wil use your gallery images in a coverflow


== Installation == 


###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`.

Then just visit your admin area and activate the plugin.

###Plugin Usage###
                                                
insert shortag [coverflow] in your post and the plugin wil use your gallery images in a coverflow




== Frequently Asked Questions ==

##How to use:##
insert shortag [coverflow] in your post and the plugin wil use your gallery images in a coverflow

##width of coverflow##
Many themes does not have the var $content_width set in the themes function file. Find the width of the content block and insert it like this

if ( ! isset( $content_width ) )
    $content_width = 548;
 
==Changelog ==
= Version 0.8 =    
*inital release

= Version 0.9 =
*Script now takes the content width from the theme
    
*Changed css to more unik names

*Put controller in class

*Can now be used in post, because of unike ids

= Version 0.9.1 = 
Minor bug in javascript fixed

= Version 0.9.2 = 
Fixed javascript bug when used in Internet explorer

= Version 0.9.3 = 
bugfixes in the image thumb generator
Added admin panel, for those who dont like to dive in the php
you can set width of coverflow. Set it equal to your content area in the theme
you can set where the image link to

 
==Upgrade Notice ==
To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

 
== Screenshots ==
1. The plugin at work.   