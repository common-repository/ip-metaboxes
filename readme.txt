=== IP Metaboxes ===
Contributors: pltchuong
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=39EEE83QU5KAW
Tags: metabox, metaboxes, custom post type, imphan, customize, flexible
Requires at least: 3.3
Tested up to: 4.4.2
Stable tag: 2.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A new metaboxes plugin, the unique one, the simplest one, and the most flexible one.

== Description ==

There is a dozen Wordpress metaboxes out there, but this one is the unique one, the simplest one, and the most flexible one. Imagine you have a post that need to display multiple of **item**, and each item have a bundle of fields, you'd need IP Metaboxes's help.

= **WHAT'S NEW on 2.1 ?** =

1. New API function to get single metabox value.
2. Add new ability to upload feature to allow you to upload MP3 audio files together with pictures.
3. Fix compatibility with Wordpress 3.8.1.

= **HIGHLIGHTED FEATURES** =

1. It has ability to add **multiple items** for each metabox. 
2. You can **change the order** of each items according to your expectation.
3. All the settings and customizations are placed under **only one page**, and it's super easy to add more and more metaboxes for any post, even custom post types. 

= How to use =
1. After install, there is a custom menu that created at the bottom of admin page, it's named "IP Metaboxes". By opening that page, you're accessing to the main setting page of IP Metaboxes.
2. Let's start to define a metabox:
	* *Metabox Name*: The name of metabox. There is no limitation of this name, you can define any easy-to-remember name for this. Changing this name would take no impact to your data.
	* *Post Types*: The post type that you would like to apply metabox to. You can select multiple types, and of course you can select custom post type.
	* *Context*: The location of metabox (left/right). Usually, *normal* and *advanced* would make your metabox stay on the left side (content section), and *side* would make it stay on the right side (publish section).
	* *Priority*: The place of metabox (top, bottom). Usually, *high* would make your metabox go up and higher than the others.
	* *Post IDs*: The special post IDs that you want to strict your metabox to display. You can enter multiple ID, separate by comma, e.g.: `102, 582`.
3. Now, let's add some fields for your metabox:
	* *Name*: The name of field. You can enter any name you want here, but please remember that, **CHANGING THE FIELD NAME WOULD MAKE YOUR OLD DATA LOST**.
	* *Type*: The type of field. 
	* *Options*: Each field type have some sort of options:
		+ Text, editor: no special options.
		+ Multiselect, select, checkbox, radio: each item is separated by comma, e.g.: `item 1, item 2, item 3`.
		+ Upload: width and height are separated by comma, e.g.: `100, 200`.
		+ Date: your expected format, default one is `dd/MM/yyyy`.
	* *Description*: The field's description. Just use it to describe a little information about the field to end-user.
	* *ID*: The field's ID. This ID is auto-generated based on your field name, you might need it in order to get a specific data as you want.
4. Finally, just save them all and you will see a sample code that show how to display the metaboxes you just added. Copy them to the template and well done.

= Public API =
Currently IP Metaboxes only public 3 methods:

1. `ipmb_get_metabox_values($metabox_id, $post_id)`
	* `$metabox_id`: you can found it under metaboxes table of IP Metaboxes setting page
	* `$post_id`: by default it would be the current post. But in order to get metaboxes of some special post you can pass it's ID here.
2. `ipmb_get_metabox_value($metabox_id, $post_id, $field_name)`
	* `$metabox_id`: you can found it under metaboxes table of IP Metaboxes setting page.
	* `$post_id`: by default it would be the current post. But in order to get metaboxes of some special post you can pass it's ID here.
  * `$field_name`: the specific field that you want to get value, can be found at metabox details table of IP Metaboxes setting page
3. `ipmb_get_metabox_images($url, $size)`
	* `$url`: the url of image, you should have this from method `ipmb_get_metabox_values` with upload field
	* `$size` : the thumbnail size, by default it's 'thumbnail' but you can change to any size you want, even the array of width and height as you usually defined.

= Known Issues =
1. When add an 'editor' with context 'side', it would automatically change to normal textarea, this happens because there is not much space for an editor when it's displaying at sidebar.
2. When you try to move items around or add new item, and if the item contain editor(s), it will be disabled and you cannot edit it's content. This is one of limitation of Wordpress editor. So just save your post and you will be able to enter text.
3. There is no link for 'Expand All' and 'Collapse All' if the context is 'side' due to the title space is too small.
	
**Please [let me know](mailto:pltchuong@gmail.com) if there is any bugs happens, or any feature that you'd like to have, I will do my best to complete it if I can :-)**
	
== Installation ==

* Upload folder `ip-metaboxes` to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

To be continued...

== Screenshots ==

1. Setting page
2. Normal Metaboxes
3. Side Metaboxes

== Changelog ==

= 2.1.1 =
* Fix bug insert media is not working

= 2.1 =
* New API function to get single metabox value.
* Add new ability to upload feature to allow you to upload MP3 audio files together with pictures.
* Fix compatibility with Wordpress 3.8.1.

= 2.0 =
* First time of plugin publication

== Upgrade notice ==

To be continued...