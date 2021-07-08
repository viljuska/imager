# Imager

Contributors: viljuska
Tags: image, images, alt, image alt, image title
Tested up to: 5.7.2
Requires at least: 5.2
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress plugin for auto generating image alt attribute. On image upload, if alt attribute is empty:

1.  Generates image clean title from filename
2.  Then, generates alt attribute from image title.
3.  Updates image with new settings

Also, on already uploaded images, it generates alt and title if not present while image is showing on the front end.
