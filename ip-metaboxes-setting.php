<?php

	// Create menu and setting page
	add_action('admin_menu', 'ipmb_menu');
	function ipmb_menu() {
		add_menu_page(__('IP Metaboxes', 'ipmb'), __('IP Metaboxes', 'ipmb'), 'manage_options', 'ipmb', 'ipmb_settings', plugins_url('/images/icon.png', __FILE__));
	}
	function ipmb_settings() {
		$ipmb = get_option('ipmb', array());
		
		$contexts = array('normal', 'advanced', 'side');
		$priorities = array('default', 'high', 'core', 'low');
		$post_types = get_post_types('', 'objects');
		$field_types = array('text', 'editor', 'multiselect', 'select', 'checkbox', 'radio', 'date', 'upload');
		
		if(isset($_GET['ipmb-action']) && $_GET['ipmb-action'] == 'edit' && isset($_GET['metabox'])) {
			$metabox = $ipmb[$_GET['metabox']];
			$ipmb_edit = true;
		} else {
			$metabox = array('name' => '', 'types' => '', 'context' => '', 'priority' => '', 'posts' => '', 'fields' => array(array('name' => '', 'type' => '', 'options' => '', 'description' => '')));
			$ipmb_edit = false;
		}
		?>
			<div class="wrap">
				<div id="ipmb-wrap">
					<div class="icon32 icon-ipmb"><br /></div>
					<h2>
						<?php
							_e('IP Metaboxes', 'ipmb');
							if($ipmb_edit) {
								?><a class="add-new-h2" href="<?php echo admin_url() . 'admin.php?page=ipmb' ?>"><?php _e('Add New Metabox', 'ipmb') ?></a><?php
							}
						?>
					</h2>
					<form method="post" action="">
						<h3>
							<?php
								if($ipmb_edit) {
									echo __('Edit Metabox', 'ipmb') . " \"" . $metabox['name'] . "\"";
								} else {
									echo __('Add New Metabox', 'ipmb');
								}
							?>
						</h3>
						
						<?php
							if($ipmb_edit) {
								?>
									<code>
										// Sample code to use, try to paste this to <em>post.php</em> or <em>page.php</em> inside your theme<br/>
										$values = ipmb_get_metabox_values('<?php echo 'ipmb_metabox_' . $_GET['metabox'] ?>');<br/>
										foreach($values as $i => $value) {<br/>
										&nbsp;&nbsp;&nbsp;&nbsp;echo "&lt;strong&gt;Fields [{$i}]: &lt;/strong&gt;&lt;br/&gt;";<br/>
										<?php
											foreach($metabox['fields'] as $field) {
												?>&nbsp;&nbsp;&nbsp;&nbsp;echo "&lt;em&gt;<?php echo $field['name'] ?>: &lt;/em&gt;{$value['<?php echo ipmb_sanitize($field) ?>']}&lt;br/&gt;";<br/><?php
											}
										?>
										&nbsp;&nbsp;&nbsp;&nbsp;echo "&lt;br/&gt;";<br/>
										}<br/>
									</code><br/>
								<?php
							}
						?>
						
						<div class="ipmb-clearfix">
							<fieldset class="ipmb-details">
								<legend><?php _e('Details', 'ipmb') ?></legend>
								
								<p class="ipmb-clearfix">
									<label for="ipmb-name"><?php _e('Metabox Name', 'ipmb') ?><span>*</span></label>
									<input type="text" name="ipmb-name" id="ipmb-name" value="<?php echo $metabox['name'] ?>"/>
								</p>
								<p class="ipmb-clearfix">
									<label for="ipmb-types"><?php _e('Post Type(s)', 'ipmb') ?></label>
									<select id="ipmb-types" multiple="" name="ipmb-types[]">
										<?php
											foreach ($post_types as $post_type) {
												?><option <?php echo is_array($metabox['types']) ? (in_array($post_type->name, $metabox['types']) ? 'selected="selected"' : '') : '' ?> value="<?php echo $post_type->name ?>"><?php echo $post_type->labels->name ?></option><?php
											}
										?>
									</select>
								</p>
								<p class="ipmb-clearfix">
									<label for="ipmb-context"><?php _e('Context', 'ipmb') ?></label>
									<select name="ipmb-context" id="ipmb-context"/>
										<?php
											foreach($contexts as $context) {
												?><option <?php echo $metabox['context'] == $context ? 'selected="selected"' : '' ?> value="<?php echo $context ?>"><?php _e($context, 'ipmb') ?></option><?php
											}
										?>
									</select>
								</p>
								<p class="ipmb-clearfix">
									<label for="ipmb-priority"><?php _e('Priority', 'ipmb') ?></label>
									<select name="ipmb-priority" id="ipmb-priority"/>
										<?php
											foreach($priorities as $priority) {
												?><option <?php echo $metabox['priority'] == $priority ? 'selected="selected"' : '' ?> value="<?php echo $priority ?>"><?php _e($priority, 'ipmb') ?></option><?php
											}
										?>
									</select>
								</p>
								<p class="ipmb-clearfix">
									<label for="ipmb-posts"><?php _e('Post IDs', 'ipmb') ?></label>
									<input type="text" name="ipmb-posts" id="ipmb-posts" value="<?php echo $metabox['posts'] ?>" />
								</p><br/>
							</fieldset>
							
							<table class="wp-list-table widefat fixed posts ipmb-fields">
								<thead>
									<tr>
										<th><?php _e('Name', 'ipmb') ?></th>
										<th><?php _e('Type', 'ipmb') ?></th>
										<th><?php _e('Options', 'ipmb') ?></th>
										<th><?php _e('Description', 'ipmb') ?></th>
										<th><?php _e('ID', 'ipmb') ?></th>
										<th width="80"><?php _e('Edit', 'ipmb') ?></th>
									</tr>
								</thead>

								<tfoot>
									<tr>
										<th><?php _e('Name', 'ipmb') ?></th>
										<th><?php _e('Type', 'ipmb') ?></th>
										<th><?php _e('Options', 'ipmb') ?></th>
										<th><?php _e('Description', 'ipmb') ?></th>
										<th><?php _e('ID', 'ipmb') ?></th>
										<th width="80"><?php _e('Edit', 'ipmb') ?></th>
									</tr>
								</tfoot>
								
								<tbody>
									<?php
										if(!$metabox['fields']) $metabox['fields'][] = array();
										foreach($metabox['fields'] as $field) {
											?>
												<tr>
													<td>
														<input type="text" class="ipmb-fields-name" value="<?php echo $field['name'] ?>" />
													</td>
													<td>
														<select class="ipmb-fields-type">
														<?php 
															foreach($field_types as $field_type) {
																?><option <?php echo $field['type'] == $field_type ? 'selected="selected"' : '' ?>><?php echo $field_type ?></option><?php
															}
														?>
														</select>
													</td>
													<td>
														<input type="text" class="ipmb-fields-options" value="<?php echo $field['options'] ?>" />
													</td>
													<td>
														<input type="text" class="ipmb-fields-description" value="<?php echo $field['description'] ?>" />
													</td>
													<td>
														<code>
															<?php
																if($ipmb_edit) {
																	echo ipmb_sanitize($field);
																} else {
																	echo '&nbsp;';
																}
															?>
														</code>
													</td>
													<td>
														<a class="ipmb-fields-add" href="#"><?php _e('Add', 'ipmb') ?></a>
														<span class="ipmb-fields-separator">|</span>
														<a class="ipmb-fields-remove" href="#"><?php _e('Remove', 'ipmb') ?></a>
													</td>
												</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
						
						<input type="hidden" name="ipmb-fields" class="ipmb-fields"/>
						<?php
							if($ipmb_edit) {
								?>
									<input type="hidden" name="ipmb-action" value="edit"/>
									<input type="hidden" name="ipmb-id" value="<?php echo $_GET['metabox'] ?>"/>
									<input type="submit" name="ipmb-update" class="button-primary" value="<?php _e('Update Metabox', 'ipmb') ?>"/>
								<?php
							} else {
								?>
									<input type="hidden" name="ipmb-action" value="add"/>
									<input type="submit" name="ipmb-update" class="button-primary" value="<?php _e('Add New Metabox', 'ipmb') ?>"/>
								<?php
							}
						?>
					</form>
					
					<table class="wp-list-table widefat fixed posts">
						<thead>
							<tr>
								<th><?php _e('Name', 'ipmb') ?></th>
								<th><?php _e('Post Type(s)', 'ipmb') ?></th>
								<th><?php _e('Context', 'ipmb') ?></th>
								<th><?php _e('Priority', 'ipmb') ?></th>
								<th><?php _e('Post ID(s)', 'ipmb') ?></th>
								<th><?php _e('ID', 'ipmb') ?></th>
								<th><?php _e('Edit', 'ipmb') ?></th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<th><?php _e('Name', 'ipmb') ?></th>
								<th><?php _e('Post Type(s)', 'ipmb') ?></th>
								<th><?php _e('Context', 'ipmb') ?></th>
								<th><?php _e('Priority', 'ipmb') ?></th>
								<th><?php _e('Post ID(s)', 'ipmb') ?></th>
								<th><?php _e('ID', 'ipmb') ?></th>
								<th><?php _e('Edit', 'ipmb') ?></th>
							</tr>
						</tfoot>
						
						<tbody>
							<?php
								if($ipmb) {
									foreach($ipmb as $i => $metabox) {
										?>
											<tr>
												<td><?php echo $metabox['name'] ?></td>
												<td><?php echo is_array($metabox['types']) ? implode(', ', $metabox['types']) : 'None' ?></td>
												<td><?php echo $metabox['context'] ?></td>
												<td><?php echo $metabox['priority'] ?></td>
												<td><?php echo $metabox['posts'] == '' ? 'All' : $metabox['posts'] ?></td>
												<td><code><?php echo 'ipmb_metabox_' . $i ?></code></td>
												<td>
													<a title="Edit Metabox" href="admin.php?page=ipmb&ipmb-action=edit&metabox=<?php echo $i ?>">Edit</a> |
													<a class="ipmb-delete" title="Delete Metabox" href="admin.php?page=ipmb&ipmb-action=delete&metabox=<?php echo $i ?>">Delete</a>
												</td>
											</tr>
										<?php
									}
								}
							?>
						</tbody>
					</table>
					
				</div>
			</div>
		<?php
	}
	
	// Save settings
	add_action('admin_init', 'ipmb_update');
	function ipmb_update() {
		$ipmb = get_option('ipmb', array());
		
		if(isset($_POST['ipmb-action']) && $_POST['ipmb-action'] == 'add') {
			$ipmb[] = array(
				'name' 		=> $_POST['ipmb-name'],
				'context' 	=> $_POST['ipmb-context'],
				'priority' 	=> $_POST['ipmb-priority'],
				'types' 	=> $_POST['ipmb-types'],
				'posts' 	=> $_POST['ipmb-posts'],
				'fields'	=> json_decode(str_replace('\\', '', $_POST['ipmb-fields']), true)
			);
			update_option('ipmb', $ipmb);
			
			header('Location: ' . admin_url() . 'admin.php?page=ipmb&ipmb-action=edit&metabox=' . (count($ipmb) - 1));
			die();
		}
		
		if(isset($_POST['ipmb-action']) && $_POST['ipmb-action'] == 'edit') {
			global $wpdb;
			$old_fields = $ipmb[$_POST['ipmb-id']]['fields'];
			$new_fields = json_decode(str_replace('\\', '', $_POST['ipmb-fields']), true);
			foreach($old_fields as $i => $old_field) {
				$new_field = $new_fields[$i];
				if($old_field['name'] != $new_field['name']) {
					$old_key = 'ipmb_metabox_' . $_POST['ipmb-id'] . '_' . ipmb_sanitize($old_field);
					$new_key = 'ipmb_metabox_' . $_POST['ipmb-id'] . '_' . ipmb_sanitize($new_field);
					$wpdb->query("UPDATE {$wpdb->postmeta} SET `meta_key` = '{$new_key}' WHERE `meta_key` = '{$old_key}'");
				}
			}
		
			$ipmb[$_POST['ipmb-id']] = array(
				'name' 		=> $_POST['ipmb-name'],
				'context' 	=> $_POST['ipmb-context'],
				'priority' 	=> $_POST['ipmb-priority'],
				'types' 	=> $_POST['ipmb-types'],
				'posts' 	=> $_POST['ipmb-posts'],
				'fields'	=> json_decode(str_replace('\\', '', $_POST['ipmb-fields']), true)
			);
			update_option('ipmb', $ipmb);
			
			header('Location: ' . admin_url() . 'admin.php?page=ipmb&ipmb-action=edit&metabox=' . $_POST['ipmb-id']);
			die();
		}

		if(isset($_GET['ipmb-action']) && $_GET['ipmb-action'] == 'delete') {
			unset($ipmb[$_GET['metabox']]);
			update_option('ipmb', $ipmb);
			
			header('Location: ' . admin_url() . 'admin.php?page=ipmb');
			die();
		}
	}
?>