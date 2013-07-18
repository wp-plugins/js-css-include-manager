<?php
/*
Plugin Name: Js Css Include Manager
Description: This plug-in is a will clean the file management. You can only manage the screen. You can also only site the screen.
Plugin URI: http://wordpress.org/extend/plugins/js-css-include-manager/
Version: 1.3.1.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=jcim&utm_campaign=1_3_1_1
Text Domain: js_css_include_manager
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain('js_css_include_manager', false, basename(dirname(__FILE__)).'/languages');

define ('JS_CSS_INCLUDE_MANAGER_VER', '1.3.1.1');
define ('JS_CSS_INCLUDE_MANAGER_PLUGIN_NAME', 'Js Css Include Manager');
define ('JS_CSS_INCLUDE_MANAGER_MANAGE_URL', admin_url('options-general.php').'?page=js_css_include_manager');
define ('JS_CSS_INCLUDE_MANAGER_RECORD_NAME', 'js_css_include_manager');
define ('JS_CSS_INCLUDE_MANAGER_PLUGIN_DIR', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/');
?>
<?php
function js_css_include_manager_add_menu() {
	// add menu
	add_options_page(__('Js Css Manager\'s Setting', 'js_css_include_manager'), __(JS_CSS_INCLUDE_MANAGER_PLUGIN_NAME, 'js_css_include_manager') , 'administrator', 'js_css_include_manager', 'js_css_include_manager_setting');

	// plugin links
	add_filter('plugin_action_links', 'js_css_include_manager_plugin_setting', 10, 2);
}



// plugin setup
function js_css_include_manager_plugin_setting($links, $file) {
	if(plugin_basename(__FILE__) == $file) {
		$support_link = '<a href="http://wordpress.org/support/plugin/js-css-include-manager" target="_blank">' . __( 'Support Forums' ) . '</a>';
		$settings_link = '<a href="'.JS_CSS_INCLUDE_MANAGER_MANAGE_URL.'">'.__('Settings').'</a>'; 
		array_unshift( $links, $support_link , $settings_link );
	}
	return $links;
}
add_action('admin_menu', 'js_css_include_manager_add_menu');





// footer text
function js_css_include_manager_admin_footer_text( $text ) {
		
	$text = '<img src="http://www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=18" width="18" /> Plugin developer : <a href="http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=footer&utm_content=jcim&utm_campaign=' . str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ) . '" target="_blank">gqevu6bsiz</a>';
		
	return $text;
}




// setting
function js_css_include_manager_setting() {

	add_filter( 'admin_footer_text' ,'js_css_include_manager_admin_footer_text' );

	$UPFN = 'sett';
	$Msg = '';
	$nonce = array( 'v' => 'jcim_update' , 'f' => 'jcim_update_field' );
	$nonce_c = wp_create_nonce( $nonce["v"] );

	if( isset( $_GET["delete"] ) && check_admin_referer( $nonce["v"] , $nonce["f"] ) ) {

		$id = $_GET["delete"];
		$Data = get_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME);
		unset($Data[$id]);
		update_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME, $Data);
		$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';

	} else if(!empty($_POST[$UPFN])) {

		// update
		if($_POST[$UPFN] == 'Y' && check_admin_referer( $nonce["v"] , $nonce["f"] ) ) {
			unset($_POST[$UPFN]);

			$Update = array();

			$type = 'update';
			if(!empty($_POST[$type])) {
				foreach ($_POST[$type] as $key => $val) {
					$Update[$key]["use"] =  strip_tags($_POST[$type][$key]["use"]);
					$Update[$key]["filetype"] =  strip_tags($_POST[$type][$key]["filetype"]);
					$Update[$key]["output"] =  strip_tags($_POST[$type][$key]["output"]);
					$Update[$key]["condition"] =  strip_tags($_POST[$type][$key]["condition"]);
					$Update[$key]["location"]["num"] =  strip_tags($_POST[$type][$key]["location"]["num"]);
					$Update[$key]["location"]["name"] =  strip_tags($_POST[$type][$key]["location"]["name"]);
				}
			}
			
			$type = 'create';
			if(!empty($_POST[$type]) && !empty($_POST[$type]["location"]["num"])) {
				$num = $_POST[$type]["location"]["num"];
				if(!empty($_POST[$type]["location"]["name"][$num])) {
					$Update[] = array(
						"use" => strip_tags($_POST[$type]["use"]),
						"filetype" => strip_tags($_POST[$type]["filetype"]),
						"output" => strip_tags($_POST[$type]["output"]),
						"condition" => strip_tags($_POST[$type]["condition"]),
						"location" => array(
							"num" => strip_tags($num),
							"name" => strip_tags($_POST[$type]["location"]["name"][$num])
						)
					);
				}
			}

			update_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME, $Update);
			$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';
		}

	}

	// include file type 
	$Filetype = array(1 => 'Javascript', 2 => 'CSS');
	
	// output
	$Output = array(1 => 'wp_head', 2 => 'wp_footer');

	// condition
	$Condition = array(1 => __( 'No further condition' , 'js_css_include_manager' ), 2 => 'is_user_logged_in()', 3=>"current_user_can('manage_options')", 4=> 'is_front_page()');

	// admin or normal
	$Use = array(1 => __( 'Admin Screen' , 'js_css_include_manager' ), 2 => __( 'Site Screen' , 'js_css_include_manager' ));

	// include location
	$Location = js_css_include_manager_location();

	// get data
	$Data = get_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME);

	// include js css
	$ReadedJs = array('jquery', 'jquery-ui-sortable');
	wp_enqueue_script('js-css-include-manager', JS_CSS_INCLUDE_MANAGER_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.js', $ReadedJs, JS_CSS_INCLUDE_MANAGER_VER);
	wp_enqueue_style('js-css-include-manager', JS_CSS_INCLUDE_MANAGER_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), JS_CSS_INCLUDE_MANAGER_VER);
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2><?php _e('Js Css Include Manager\'s Settings', 'js_css_include_manager'); ?></h2>
	<?php echo $Msg; ?>

	<div class="metabox-holder columns-2">

		<div class="postbox-container" id="postbox-container-1">

			<form id="js_css_include_manager_form" method="post" action="<?php echo JS_CSS_INCLUDE_MANAGER_MANAGE_URL; ?>">
				<input type="hidden" name="<?php echo $UPFN; ?>" value="Y">
				<?php wp_nonce_field( $nonce["v"] , $nonce["f"] ); ?>
		
				<?php $type = 'create'; ?>
				<div id="<?php echo $type; ?>">
					<h3><?php _e('Set a file to include:', 'js_css_include_manager'); ?></h3>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $type; ?>_use"><?php _e('Use', 'js_css_include_manager'); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[use]" id="<?php echo $type; ?>_use">
										<?php foreach($Use as $key => $val) : ?>
											<?php if(!empty($val)) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_filetype"><?php _e('File Type', 'js_css_include_manager'); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[filetype]" id="<?php echo $type; ?>_filetype">
										<?php foreach($Filetype as $key => $val) : ?>
											<?php if(!empty($val)) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_output"><?php _e('Output', 'js_css_include_manager'); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[output]" id="<?php echo $type; ?>_output">
										<?php foreach($Output as $key => $val) : ?>
											<?php if(!empty($val)) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_condition"><?php _e('Condition', 'js_css_include_manager'); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[condition]" id="<?php echo $type; ?>condition">
										<?php foreach($Condition as $key => $val) : ?>
											<?php if(!empty($val)) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
									<code><a href="http://codex.wordpress.org/Function_Reference/is_user_logged_in" target="_blank">is_user_logged_in()</a></code>
									<code><a href="http://codex.wordpress.org/Function_Reference/current_user_can" target="_blank">current_user_can()</a></code>
									<code><a href="http://codex.wordpress.org/Function_Reference/is_front_page" target="_blank">is_front_page()</a></code>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_location"><?php _e('Location', 'js_css_include_manager'); ?></label> *</th>
								<td>
									<ul>
										<?php foreach($Location as $key => $val) : ?>
											<?php if(!empty($val["name"])) : ?>
												<li>
													<label><input type="radio" name="<?php echo $type; ?>[location][num]" value="<?php echo $key; ?>" /><?php echo _e($val["name"], 'js_css_include_manager'); ?></label>
													<?php if(!empty($val["location"])) : ?>
														<code><?php echo $val["location"]; ?></code>
														<input type="text" name="<?php echo $type; ?>[location][name][<?php echo $key; ?>]" class="regular-text disabled" disabled="disabled" />
													<?php else: ?>
														<code>http://sample.com/sample.css or http://sample.com/sample.js</code>
														<input type="text" name="<?php echo $type; ?>[location][name][<?php echo $key; ?>]" class="large-text disabled" disabled="disabled" />
													<?php endif; ?>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="button" class="button-primary" value="<?php _e('Save'); ?>" />
					</p>
				</div>
		
		
				<?php $type = 'update'; ?>
				<div id="<?php echo $type; ?>">
					<h3><?php _e('Include setting that you created.', 'js_css_include_manager'); ?></h3>
					<?php if(!empty($Data)) : ?>
		
						<table cellspacing="0" class="widefat fixed">
							<thead>
								<tr>
									<th class="use"><?php _e('Use', 'js_css_include_manager'); ?></th>
									<th class="filetype"><?php _e('File Type', 'js_css_include_manager'); ?></th>
									<th class="output"><?php _e('Output', 'js_css_include_manager'); ?></th>
									<th class="condition"><?php _e('Condition', 'js_css_include_manager'); ?></th>
									<th class="location"><?php _e('Location', 'js_css_include_manager'); ?></th>
									<th class="operation">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($Data as $key => $val) : ?>
									<tr>
										<td class="use">
											<select name="<?php echo $type; ?>[<?php echo $key; ?>][use]">
												<?php foreach($Use as $usenum => $usetype) : ?>
													<?php $Selected = ''; ?>
													<?php if(!empty($usetype)) : ?>
														<?php if($usenum == strip_tags($val["use"])) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $usenum; ?>" <?php echo $Selected; ?>><?php echo $usetype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<span><?php echo $Use[strip_tags($val["use"])]; ?></span>
										</td>
										<td class="filetype">
											<select name="<?php echo $type; ?>[<?php echo $key; ?>][filetype]">
												<?php foreach($Filetype as $filenum => $filetype) : ?>
													<?php $Selected = ''; ?>
													<?php if(!empty($filetype)) : ?>
														<?php if($filenum == strip_tags($val["filetype"])) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $filenum; ?>" <?php echo $Selected; ?>><?php echo $filetype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<span><?php echo $Filetype[strip_tags($val["filetype"])]; ?></span>
										</td>
										<td class="output">
											<select name="<?php echo $type; ?>[<?php echo $key; ?>][output]">
												<?php foreach($Output as $outputnum => $outputtype) : ?>
													<?php $Selected = ''; ?>
													<?php if(!empty($outputtype)) : ?>
														<?php if($outputnum == strip_tags($val["output"])) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $outputnum; ?>" <?php echo $Selected; ?>><?php echo $outputtype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<span><?php echo $Output[strip_tags($val["output"])]; ?></span>
										</td>
										<td class="condition">
											<select name="<?php echo $type; ?>[<?php echo $key; ?>][condition]">
												<?php foreach($Condition as $condnum => $condtype) : ?>
													<?php $Selected = ''; ?>
													<?php if(!empty($condtype)) : ?>
														<?php if(!empty($val["condition"]) && $condnum == strip_tags($val["condition"])) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $condnum; ?>" <?php echo $Selected; ?>><?php echo $condtype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<span>
												<?php if( !empty( $val["condition"] ) ) : ?>
													<?php echo $Condition[strip_tags($val["condition"])]; ?>
												<?php else : ?>
													<?php echo $Condition[1]; ?>
												<?php endif; ?>
											</span>
										</td>
										<td class="location">
											<ul>
												<?php foreach($Location as $location_num => $location_val) : ?>
													<?php $Checked = ""; ?>
													<?php $ClassDis = 'disabled'; ?>
													<?php $Disabled = 'disabled="disabled"'; ?>
													<?php $Value = ''; ?>
													<?php if(!empty($location_val["name"])) : ?>
														<?php if($val["location"]["num"] == $location_num) : ?>
															<?php $Checked = 'checked="checked"'; ?>
															<?php $ClassDis = ''; ?>
															<?php $Disabled = ''; ?>
															<?php $Value = strip_tags($val["location"]["name"]); ?>
														<?php endif; ?>
														<li>
															<label><input type="radio" name="<?php echo $type; ?>[<?php echo $key; ?>][location][num]" value="<?php echo $location_num; ?>" <?php echo $Checked; ?> /><?php echo $location_val["name"]; ?></label>
															<?php if(!empty($location_val["location"])) : ?>
																<code><?php echo $location_val["location"]; ?></code>
															<?php else: ?>
																<code>http://sample.com/sample.css or http://sample.com/sample.js</code>
															<?php endif; ?>
															<input type="text" name="<?php echo $type; ?>[<?php echo $key; ?>][location][name]" class="large-text <?php echo $ClassDis; ?>" <?php echo $Disabled; ?> value="<?php echo $Value; ?>" />
														</li>
													<?php endif; ?>
												<?php endforeach; ?>
											</ul>
		
											<span>
												<?php $FileUrl = $Location[strip_tags($val["location"]["num"])]["location"].strip_tags($val["location"]["name"]); ?>
												<a href="<?php echo $FileUrl; ?>" target="_blank"><?php echo esc_html($FileUrl); ?></a>
												<?php if(!empty($FileUrl)) : ?>
													<?php $response = wp_remote_get( $FileUrl ); ?>
													<?php if ( is_wp_error( $response ) ) : ?>
														<br /><code>No Header</code>
													<?php elseif( 200 != wp_remote_retrieve_response_code( $response ) ) : ?>
														<br /><code><?php echo wp_remote_retrieve_response_code( $response ); ?></code>
													<?php endif; ?>
												<?php endif; ?>
											</span>
										</td>
										<td class="operation">
											<span>
												<a class="edit" href="javascript:void(0)"><?php _e('Edit'); ?></a>
												&nbsp;|&nbsp;
												<a class="delete" href="<?php echo JS_CSS_INCLUDE_MANAGER_MANAGE_URL; ?>&delete=<?php echo $key; ?>&<?php echo $nonce["f"]; ?>=<?php echo $nonce_c; ?>"><?php _e('Delete'); ?></a>
											</span>
											<p class="submit">
												<input type="button" class="button-primary" value="<?php _e('Save'); ?>" />
											</p>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
		
					<?php else: ?>
		
						<p><?php _e('Not created include setting.', 'js_css_include_manager'); ?></p>
		
					<?php endif; ?>
				</div>

			</form>

		</div>
		
		<div class="postbox-container" id="postbox-container-2">
			
			<div class="stuffbox" style="border-color: #FFC426; border-width: 3px;">
				<h3 style="background: #FFF2D0; border-color: #FFC426;"><span class="hndle"><?php _e( 'How may I help you?' , 'js_css_include_manager' ); ?></span></h3>
				<div class="inside">
					<p style="float: right;">
						<img src="http://www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=46" width="46" /><br />
						<a href="http://gqevu6bsiz.chicappa.jp/contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" target="_blank">gqevu6bsiz</a>
					</p>
					<p><?php _e( 'I am good at Admin Screen Customize.' , 'js_css_include_manager' ); ?></p>
					<p><?php _e( 'Please consider the request to me if it is good.' , 'js_css_include_manager' ); ?></p>
					<p>
						<a href="http://wpadminuicustomize.com/blog/category/example/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" target="_blank"><?php _e ( 'Example Customize' , 'js_css_include_manager' ); ?></a> :
						<a href="http://gqevu6bsiz.chicappa.jp/contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" target="_blank"><?php _e( 'Contact me' , 'js_css_include_manager' ); ?></a></p>
				</div>
			</div>

			<div class="stuffbox" style="background: #87BCE4; border: 1px solid #227499;">
				<div class="inside">
					<p style="color: #FFFFFF; font-size: 20px;"><?php _e( 'Please donate.' , 'js_css_include_manager' ); ?></p>
					<p style="text-align: center;">
						<a href="http://gqevu6bsiz.chicappa.jp/please-donation/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" class="button-primary" target="_blank"><?php _e( 'Donate' , 'js_css_include_manager' ); ?></a>
					</p>
				</div>
			</div>

				<div class="stuffbox" id="aboutbox">
					<h3><span class="hndle"><?php _e( 'About plugin' , 'js_css_include_manager' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Version check' , 'js_css_include_manager' ); ?> : 3.4.2 - 3.6 RC1</p>
						<ul>
							<li><a href="http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" target="_blank"><?php _e( 'Developer\'s site' , 'js_css_include_manager' ); ?></a></li>
							<li><a href="http://wordpress.org/support/plugin/js-css-include-manager" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
							<li><a href="http://wordpress.org/support/view/plugin-reviews/js-css-include-manager" target="_blank"><?php _e( 'Reviews' , 'js_css_include_manager' ); ?></a></li>
							<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
							<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
						</ul>
					</div>
				</div>

				<div class="stuffbox" id="usefulbox">
					<h3><span class="hndle"><?php _e( 'Useful plugins' , 'js_css_include_manager' ); ?></span></h3>
					<div class="inside">
						<p><strong><a href="http://wpadminuicustomize.com/?utm_source=use_plugin&utm_medium=side&utm_content=jcim&utm_campaign=<?php echo str_replace( '.' , '_' , JS_CSS_INCLUDE_MANAGER_VER ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
						<p class="description"><?php _e( 'Customize a variety of screen management.' , 'js_css_include_manager' ); ?></p>
						<p><strong><a href="http://wordpress.org/extend/plugins/custom-options-plus-post-in/" target="_blank">Custom Options Plus Post in</a></strong></p>
						<p class="description"><?php _e( 'The plugin that allows you to add the value of the options. Option value that you have created, can be used in addition to the template tag, Short code can be used in the body of the article.' , 'js_css_include_manager' ); ?></p>
						<p><strong><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank">Announce from the Dashboard</a></strong></p>
						<p class="description"><?php _e( 'Announce to display the dashboard. Change the display to a different user role.' , 'js_css_include_manager' ); ?></p>
						<p>&nbsp;</p>
					</div>
				</div>

		</div>

		
		<div class="clear"></div>

	</div>


</div>
<?php
}



// location load
function js_css_include_manager_location() {
	$Location = array(
		1 => array(
			'name' => __('This Plugin Directory', 'js_css_include_manager'),
			'location' => JS_CSS_INCLUDE_MANAGER_PLUGIN_DIR
		),
		2 => array(
			'name' => __('Other Plugin Directory', 'js_css_include_manager'),
			'location' => content_url().'/plugins/'
		),
		3 => array(
			'name' => __('The Active Theme\'s Directory', 'js_css_include_manager').' <span class="description">('.get_template().')</span>',
			'location' => get_template_directory_uri().'/'
		),
		4 => array(
			'name' => __('Other Theme\'s Directory', 'js_css_include_manager'),
			'location' => content_url().'/themes/'
		),
		5 => array(
			'name' => __('External File', 'js_css_include_manager'),
			'location' => ''
		),
	);

	return $Location;
}





// include file
function js_css_include_manager_include($Data = array()) {

	if(!empty($Data)) {
		foreach($Data as $type => $Val) {
			if($type == 'js') {
				if(!empty($Val)) {
					foreach($Val as $key => $File) {
						if( !empty( $File ) ) {
							if( $File["output"] == "1" ) {
								wp_enqueue_script('js_css_include_manager-'.$File["dn"], $File["file"], array( 'jquery' ));
							} elseif( $File["output"] == "2" ) {
								wp_enqueue_script('js_css_include_manager-'.$File["dn"], $File["file"], array( 'jquery' ) , false , true );
							}
						}
					}
				}
			} else if($type == 'css') {
				if(!empty($Val)) {
					foreach($Val as $key => $File) {
						if( !empty( $File ) ) {
							wp_enqueue_style('js_css_include_manager-'.$File["dn"], $File["file"]);
						}
					}
				}
			}
		}
	}
}



// data filter
function js_css_include_manager_include_filter( $Setting ) {
	$Location = js_css_include_manager_location();
	$Data = get_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME);

	$DataFilt = array();
	if(!empty($Data)) {
		foreach($Data as $key => $Val) {
			// use = admin or normal
			// filetype = javascript or css
			// output =  wp_head or wp_footer
			if(!empty($Val["use"]) && !empty($Val["filetype"]) && !empty($Val["output"]) && !empty($Val["location"])) {
				if($Val["use"] == $Setting["use"] && $Val["output"] == $Setting["output"]) {
					$File = strip_tags($Location[$Val["location"]["num"]]["location"].strip_tags($Val["location"]["name"]));
					$Val["file"] = $File;
					$Val["dn"] = $key;
					if(isset($Val['condition'])) {
						if (2 == $Val['condition']) {
							if (!is_user_logged_in()) continue;
						} elseif (3 == $Val['condition']) {
							if (!current_user_can('manage_options')) continue;
						} elseif (4 == $Val['condition']) {
							if (!is_front_page()) continue;
						}
					}
					if($Val["filetype"] == 1) {
						$DataFilt['js'][] = $Val;
					} else if($Val["filetype"] == 2) {
						$DataFilt['css'][] = $Val;
					}
				}
			}
		}
	}

	return $DataFilt;
}

function js_css_include_manager_doinclude($Setting) {
	$Data = js_css_include_manager_include_filter($Setting);

	if(!empty($Data)) {
		js_css_include_manager_include($Data);
	}
}

// admin header include
function js_css_include_manager_admin_head() {
	js_css_include_manager_doinclude(array("use" => 1, "output" => 1));
}
add_action('admin_enqueue_scripts', 'js_css_include_manager_admin_head');



// admin footer include
function js_css_include_manager_admin_foot() {
	js_css_include_manager_doinclude(array("use" => 1, "output" => 2));
}
add_action('admin_footer', 'js_css_include_manager_admin_foot');



// site header include
function js_css_include_manager_normal_head() {
	js_css_include_manager_doinclude(array("use" => 2, "output" => 1));
}
add_action('wp_enqueue_scripts', 'js_css_include_manager_normal_head');



// site footer include
function js_css_include_manager_normal_foot() {
	js_css_include_manager_doinclude(array("use" => 2, "output" => 2));
}
add_action('get_footer', 'js_css_include_manager_normal_foot');


?>