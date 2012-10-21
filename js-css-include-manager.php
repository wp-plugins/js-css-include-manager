<?php
/*
Plugin Name: Js Css Include Manager
Description: Javascript file and Css file for include will manage.
Plugin URI: http://gqevu6bsiz.chicappa.jp
Version: 1.0.0
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/author/admin/
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

define ('JS_CSS_INCLUDE_MANAGER_VER', '1.0.0');
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
		$settings_link = '<a href="'.JS_CSS_INCLUDE_MANAGER_MANAGE_URL.'">'.__('Settings').'</a>'; 
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_action('admin_menu', 'js_css_include_manager_add_menu');



// setting
function js_css_include_manager_setting() {
	$UPFN = 'sett';
	$Msg = '';

	if(isset($_GET["delete"])) {

		$id = $_GET["delete"];
		$Data = get_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME);
		unset($Data[$id]);
		update_option(JS_CSS_INCLUDE_MANAGER_RECORD_NAME, $Data);
		$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';

	} else if(!empty($_POST[$UPFN])) {

		// update
		if($_POST[$UPFN] == 'Y') {
			unset($_POST[$UPFN]);

			$Update = array();

			$type = 'update';
			if(!empty($_POST[$type])) {
				foreach ($_POST[$type] as $key => $val) {
					$Update[$key]["use"] =  strip_tags($_POST[$type][$key]["use"]);
					$Update[$key]["filetype"] =  strip_tags($_POST[$type][$key]["filetype"]);
					$Update[$key]["output"] =  strip_tags($_POST[$type][$key]["output"]);
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
	$Filetype = array(1 => 'Javascript', 2 => 'Cascading Style Sheets');
	
	// output
	$Output = array(1 => 'wp_head', 2 => 'wp_footer');

	// admin or normal
	$Use = array(1 => 'admin', 2 => 'normal');

	// ファイルを読み込む階層
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
	<h2><?php _e('Js Css Include Manager\'s Setting', 'js_css_include_manager'); ?></h2>
	<?php echo $Msg; ?>
	<p>&nbsp;</p>

	<form id="js_css_include_manager_form" method="post" action="<?php echo JS_CSS_INCLUDE_MANAGER_MANAGE_URL; ?>">
		<input type="hidden" name="<?php echo $UPFN; ?>" value="Y">
		<?php wp_nonce_field(-1, '_wpnonce', false); ?>

		<?php $type = 'create'; ?>
		<div id="<?php echo $type; ?>">
			<h3><?php _e('Setting an include file.', 'js_css_include_manager'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="<?php echo $type; ?>_use"><?php _e('Use', 'js_css_include_manager'); ?></label> *</th>
						<td>
							<select name="<?php echo $type; ?>[use]" id="<?php echo $type; ?>_use">
								<?php foreach($Use as $key => $val) : ?>
									<?php if(!empty($val)) : ?>
										<option value="<?php echo $key; ?>"><?php echo _e($val, 'js_css_include_manager'); ?></option>
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
												<option value="<?php echo $usenum; ?>" <?php echo $Selected; ?>><?php echo _e($usetype, 'js_css_include_manager'); ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
									<span><?php echo _e($Use[strip_tags($val["use"])], 'js_css_include_manager'); ?></span>
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
											<?php $Gh = @get_headers($FileUrl); ?>
											<?php if(empty($Gh[0])) : ?>
												<br /><code>No Header</code>
											<?php elseif(!preg_match('#^HTTP/.*\s+[200|302]+\s#i', $Gh[0])) : ?>
												<br /><code><?php echo $Gh[0]; ?></code>
											<?php endif; ?>
										<?php endif; ?>
									</span>
								</td>
								<td class="operation">
									<span>
										<a class="edit" href="javascript:void(0)"><?php _e('Edit'); ?></a>
										&nbsp;|&nbsp;
										<a class="delete" href="<?php echo JS_CSS_INCLUDE_MANAGER_MANAGE_URL; ?>&delete=<?php echo $key; ?>"><?php _e('Delete'); ?></a>
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
			'name' => __('This Active Theme\'s Directory', 'js_css_include_manager').' <span class="description">('.get_template().')</span>',
			'location' => get_template_directory_uri().'/'
		),
		4 => array(
			'name' => __('Other themes Directory', 'js_css_include_manager'),
			'location' => content_url().'/themes/'
		),
		5 => array(
			'name' => __('External file', 'js_css_include_manager'),
			'location' => ''
		),
	);

	return $Location;
}



// include file
function js_css_include_manager_include($Data = array()) {
	
	if(!empty($Data)) {
		foreach($Data as $key => $Val) {
			if($key == 'js') {
				if(!empty($Val)) {
					foreach($Val as $num => $File) {
						wp_enqueue_script('js_css_include_manager-'.$num, $File, array( 'jquery' ));
					}
				}
			} else if($key == 'css') {
				if(!empty($Val)) {
					foreach($Val as $num => $File) {
						wp_enqueue_style('js_css_include_manager-'.$num, $File);
					}
				}
			}
		}
	}
}



// data filter
function js_css_include_manager_include_filter($Setting) {
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
					if($Val["filetype"] == 1) {
						$DataFilt['js'][] = $File;
					} else if($Val["filetype"] == 2) {
						$DataFilt['css'][] = $File;
					}
				}
			}
		}
	}

	return $DataFilt;
}



// admin header include
function js_css_include_manager_admin_head() {
	$Setting = array("use" => 1, "output" => 1);
	$Data = js_css_include_manager_include_filter($Setting);

	if(!empty($Data)) {
		js_css_include_manager_include($Data);
	}
}
add_action('admin_enqueue_scripts', 'js_css_include_manager_admin_head');



// admin footer include
function js_css_include_manager_admin_foot() {
	$Setting = array("use" => 1, "output" => 2);
	$Data = js_css_include_manager_include_filter($Setting);

	if(!empty($Data)) {
		js_css_include_manager_include($Data);
	}
}
add_action('admin_footer', 'js_css_include_manager_admin_foot');



// normal header include
function js_css_include_manager_normal_head() {
	$Setting = array("use" => 2, "output" => 1);
	$Data = js_css_include_manager_include_filter($Setting);

	if(!empty($Data)) {
		js_css_include_manager_include($Data);
	}
}
add_action('wp_enqueue_scripts', 'js_css_include_manager_normal_head');



// normal footer include
function js_css_include_manager_normal_foot() {
	$Setting = array("use" => 2, "output" => 2);
	$Data = js_css_include_manager_include_filter($Setting);

	if(!empty($Data)) {
		js_css_include_manager_include($Data);
	}
}
add_action('get_footer', 'js_css_include_manager_normal_foot');

?>