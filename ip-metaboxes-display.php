<?php
	// Display metaboxes
	add_action('add_meta_boxes', 'ipmb_add_metaboxes');
	function ipmb_add_metaboxes() {
		global $post;
		$ipmb = get_option('ipmb', array());
		foreach($ipmb as $id => $metabox) {
			if(!is_array($metabox['types'])) continue;
			foreach($metabox['types'] as $type) {
				$posts = preg_split('/,\s?/', $metabox['posts']);
				if(is_array($posts) && $posts[0] != '') {
					if(in_array($post->ID, $posts)) {
						add_meta_box('ipmb_metabox_' . $id, __($metabox['name'], 'ipmb'), 'ipmb_add_metabox', $type, $metabox['context'], $metabox['priority'], array($metabox['context'] => $metabox['fields']));
					}
				} else {
					add_meta_box('ipmb_metabox_' . $id, __($metabox['name'], 'ipmb'), 'ipmb_add_metabox', $type, $metabox['context'], $metabox['priority'], array($metabox['context'] => $metabox['fields']));
				}	
			}
		}
	}
	function ipmb_add_metabox($post, $args) {
		$fields = $args['args'];
		
		foreach($args['args'] as $context => $fields) {
			$repeat = get_post_meta($post->ID, $args['id'], true);
			if(!$repeat) $repeat = 1;
			?>
				<input name="<?php echo $args['id'] ?>" type="hidden" value="<?php echo $repeat ?>" />
				<div class="ipmb-metabox">
					<?php
						for($i = 0; $i < $repeat; $i++) {
							?>
								<dl>
									<?php
										foreach($fields as $field) {
											$field_slug = $args['id'] . '_' . str_replace('-', '_', sanitize_title($field['name']));
											$field_values = get_post_meta($post->ID, $field_slug, true);
											$field_options = preg_split('/,\s?/', $field['options']);
											?>
												<dt><?php echo $field['name'] ?></dt>
												<dd>
													<div>
														<?php
															switch($field['type']) {
																case 'text':
																	?><input name="<?php echo $field_slug ?>[]" class="ipmb-metabox-input" type="text" value="<?php echo (isset($field_values[$i]) && $field_values[$i]) ? $field_values[$i] : '' ?>" /><?php
																	break;
																case 'editor':
																	if ($context == 'side') {
																		?><textarea name="<?php echo $field_slug ?>[]" class="ipmb-metabox-input"><?php echo (isset($field_values[$i]) && $field_values[$i]) ? $field_values[$i] : '' ?></textarea><?php
																	} else {
																		wp_editor((isset($field_values[$i])  && $field_values[$i]) ? $field_values[$i] : '', str_replace('_', '', $field_slug . $i), array('textarea_name' => $field_slug . '[]', 'textarea_rows' => 6));
																	}
																	break;
																case 'select':
																	?>
																		<select name="<?php echo $field_slug ?>[]" class="ipmb-metabox-input-small">
																			<?php
																				foreach($field_options as $option) {
																					?><option <?php echo (isset($field_values[$i]) && $field_values[$i] == $option) ? 'selected="selected"' : '' ?> value="<?php echo $option ?>"><?php echo $option ?></option><?php
																				}
																			?>
																		</select>
																	<?php
																	break;
																case 'multiselect':
																	?>
																		<select multiple="multiple" name="<?php echo $field_slug ?>[<?php echo $i ?>][]" class="ipmb-metabox-input-small">
																			<?php
																				foreach($field_options as $option) {
																					?><option <?php echo (isset($field_values[$i]) && $field_values[$i]) ? (in_array($option, $field_values[$i]) ? 'selected="selected"' : '') : '' ?> value="<?php echo $option ?>"><?php echo $option ?></option><?php
																				}
																			?>
																		</select>
																	<?php
																	break;
																case 'checkbox':
																	foreach($field_options as $option) {
																		?><input name="<?php echo $field_slug ?>[<?php echo $i ?>][]" <?php echo (isset($field_values[$i]) && $field_values[$i]) ? (in_array($option, $field_values[$i]) ? 'checked="checked"' : '') : '' ?> value="<?php echo $option ?>" type="checkbox" /> &nbsp; <?php echo $option ?><br/><?php
																	}
																	break;
																case 'radio':
																	foreach($field_options as $option) {
																		?><input name="<?php echo $field_slug ?>[<?php echo $i ?>][]" <?php echo (isset($field_values[$i]) && $field_values[$i]) ? (in_array($option, $field_values[$i]) ? 'checked="checked"' : '') : '' ?> value="<?php echo $option ?>" type="radio" /> &nbsp; <?php echo $option ?><br/><?php
																	}
																	break;
																case 'date':
																	?><input name="<?php echo $field_slug ?>[]" class="ipmb-metabox-datepicker ipmb-metabox-input-small" type="text" value="<?php echo (isset($field_values[$i]) && $field_values[$i]) ? date('m/d/Y', $field_values[$i]) : '' ?>" /><?php
																	break;
																case 'upload':
																	if(isset($field_values[$i]) && $field_values[$i]) {
																		?>
																			<a onClick="ipmb_metabox_upload_remove(this); return false;" id="<?php echo $field_slug . '_' . $i ?>" href="#" class="ipmb-metabox-upload ipmb-metabox-upload-remove" title="<?php _e('Add Media', 'ipmb') ?>">
																				<span></span>
																				<?php echo wp_get_attachment_image($field_values[$i], 'ipmb-thumbnail', true) ?>
																			</a>
																			<input name="<?php echo $field_slug ?>[]" type="hidden" value="<?php echo $field_values[$i] ?>" />
																		<?php
																	} else {
																		?>
																			<a onClick="ipmb_metabox_upload_insert(this); return false;" id="<?php echo $field_slug . '_' . $i ?>" href="#" class="insert-media ipmb-metabox-upload ipmb-metabox-upload-insert" title="<?php _e('Add Media', 'ipmb') ?>">
																				<span></span>
																			</a>
																			<input name="<?php echo $field_slug ?>[]" type="hidden" />
																		<?php
																	}																				
																	break;
															}
														?>
													</div>
													<?php
														if($field['description']) {
															?><small class="ipmb-metabox-description"><?php echo $field['description'] ?></small><?php
														}
													?>
												</dd>
											<?php
										}
									?>
									<div>
										<span class="ipmb-metabox-add"><a href="#">Add</a></span>
										<span class="ipmb-metabox-remove"> &nbsp; | &nbsp; <a href="#">Remove</a></span>
										<span class="ipmb-metabox-collapse"> &nbsp; | &nbsp; <a href="#">Collapse</a></span>
										<span class="ipmb-metabox-move"> &nbsp; | &nbsp; Move</span>
									</div>
								</dl>
							<?php
						}
					?>
				</div>
				
				<?php
					if ($context != 'side') {
						?>
							<a class="ipmb-metabox-all ipmb-metabox-expand-all" href="#">Expand All &#x25BC;</a>
							<a class="ipmb-metabox-all ipmb-metabox-collapse-all" href="#">Collapse All &#x25B2;</a>
						<?php
					}
				?>
			<?php
		}
	}

	// Handle media upload
	add_action('wp_ajax_ipmb_upload', 'ipmb_upload');
	function ipmb_upload() {
		global $wpdb;
		$attachment_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid='{$_POST['attachment']}'");
		echo $attachment_id . '|' . wp_get_attachment_image($attachment_id, 'ipmb-thumbnail', true);
		die();
	}
	
	// Save metaboxes
	add_action('save_post', 'ipmb_save_metabox');
	function ipmb_save_metabox($post_id) {
		$ipmb = get_option('ipmb', array());
		$total_metaboxes = 0;
		foreach($_POST as $key => $values) {
			if(strpos($key, 'ipmb_metabox') === false) continue;
			$key_splitted = explode('_', str_replace('ipmb_metabox_', '', $key), 2);
			if(count($key_splitted) == 1) {
				$total_metaboxes = $values;
				update_post_meta($post_id, $key, $values);
			} else {
				foreach($ipmb[$key_splitted[0]]['fields'] as $field) {
					if($key_splitted[1] != str_replace('-', '_', sanitize_title($field['name']))) continue;
					$new_values = array();
					for($i = 0; $i < $total_metaboxes; $i++) {
						$value = $values[$i];
						if($value) {
							if($field['type'] == 'date') {
								$value = strtotime($value);
							} else if($field['type'] == 'text') {
								$value = str_replace('"', '&quot;', $value);
							}
							$new_values[$i] = $value;
						} else {
							$new_values[$i] = false;
						}
					}
					update_post_meta($post_id, $key, $new_values);
				}
			}
		}
	}
?>