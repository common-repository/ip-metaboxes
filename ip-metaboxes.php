<?php
	/*
		Plugin Name: IP Metaboxes
		Plugin URI: http://imphan.com/
		Description: A new metaboxes plugin, the unique one, the simplest one, and the most flexible one.
		Author: Phan Chuong
		Author URI: http://imphan.com/
		Version: 2.1.1
	*/

	require_once('ip-metaboxes-setting.php');
	require_once('ip-metaboxes-display.php');

	// Inject js and css
	add_action('admin_init', 'ipmb_js');
	function ipmb_js() {
		global $pagenow;
		if ($pagenow == 'post-new.php' || $pagenow == 'post.php' || ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'ipmb')) {
			wp_enqueue_script('ip-metaboxes', plugins_url('/ip-metaboxes.js', __FILE__), array('jquery-ui-sortable', 'jquery-ui-datepicker'), true, true);
			wp_enqueue_style('ip-metaboxes', plugins_url('/ip-metaboxes.css', __FILE__), array(), true);
			wp_enqueue_style('datepicker', plugins_url('/datepicker.css', __FILE__), array(), true);
		}
	}
	
	// Apply custom thumbnail size based on user's metaboxes
	add_image_size('ipmb-thumbnail', 75, 75, true);
	$ipmb = get_option('ipmb', array());
	foreach($ipmb as $metabox_id => $metabox) {
		foreach($metabox['fields'] as $field) {
			if($field['type'] == 'upload' && $field['options']) {
				$thumbnail_size = preg_split('/,\s?/', $field['options']);
				add_image_size('ipmb_metabox_' . $metabox_id . '_' . ipmb_sanitize($field), $thumbnail_size[0], $thumbnail_size[1], true);
			}
		}
	}
	
	// Add configuration URL inside plugin page
	add_filter('plugin_row_meta', 'ipmb_url', 10, 4);
	function ipmb_url($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links[] = '<span class="delete"><a href="' . admin_url() . 'admin.php?page=ipmb">' . __('Configure IP Metaboxes', 'ipmb') . '</a></span>';
		}
		return $links;
	}
	
	// Standardize metabox's field names
	function ipmb_sanitize($field) {
		return str_replace('-', '_', sanitize_title($field['name']));
	}
	
	// Get metaboxes values
	function ipmb_get_metabox_values($metabox, $id = null) {
		if (!$id) {
			global $post;
			$id = $post->ID;
		}
		$metaboxes = get_post_meta($id, $metabox, true);
		
		$ipmb = get_option('ipmb', array());
		$fields = $ipmb[str_replace('ipmb_metabox_', '', $metabox)]['fields'];
		
		$values = array();
		for($i = 0; $i < $metaboxes; $i++) {
			foreach($fields as $field) {
				$field_values = get_post_meta($id, $metabox . '_' . ipmb_sanitize($field), true);
				
				if(isset($field_values[$i]) && $field_values[$i]) {
					$field_value = $field_values[$i];
					
					if($field['type'] == 'upload') {
						if(strpos(get_post_mime_type($field_values[$i]), 'image') === false) {
							$field_value = wp_get_attachment_url($field_values[$i]);
						} else {
							$attachment = wp_get_attachment_image_src($field_values[$i], $metabox . '_' . ipmb_sanitize($field), true);
							$field_value = $attachment[0];
						}
					} else if($field['type'] == 'date') {
						$field_value = date('m/d/Y', $field_values[$i]);
					} else if($field['type'] == 'editor') {
						$field_value = wpautop($field_values[$i]);
					}
					
				} else {
					$field_value = '';
				}
				
				$values[$i][ipmb_sanitize($field)] = $field_value;
			}
		}
		return $values;
	}
  
  // Get single metaboxes value
  function ipmb_get_metabox_value($metabox, $id = null, $key = null) {
    $values = ipmb_get_metabox_values($metabox, $id);
    if($key) {
      foreach($values[0] as $i => $value) {
        return $values[0][$i];
      }
    } else {
      return $values[0];
    }
  }

	// Get metaboxes images
	function ipmb_get_metabox_images($url, $size = 'thumbnail') {
		global $wpdb;
		$url = preg_replace('/-\d+x\d+/', '', $url);
		$attachment_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid='{$url}'");
		$attachment = wp_get_attachment_image_src($attachment_id, $size, true);
		return $attachment[0];
	}
?>