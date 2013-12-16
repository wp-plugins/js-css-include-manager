<?php
/*
Plugin Name: Js Css Include Manager
Description: This plug-in is a will clean the file management. You can only manage the screen. You can also only site the screen.
Plugin URI: http://wordpress.org/extend/plugins/js-css-include-manager/
Version: 1.3.3
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=jcim&utm_campaign=1_3_3
Text Domain: jcim
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



class JS_CSS_include_manager
{

	var $Ver,
		$Name,
		$Dir,
		$Url,
		$Site,
		$AuthorUrl,
		$ltd,
		$Record,
		$Record_d,
		$Record_dw,
		$PageSlug,
		$PluginSlug,
		$ColumnCount,
		$Nonces,
		$Schema,
		$UPFN,
		$DonateKey,
		$MsgQ,
		$Msg;


	function __construct() {
		$this->Ver = '1.3.3';
		$this->Name = 'Js Css Include Manager';
		$this->Dir = plugin_dir_path( __FILE__ );
		$this->Url = plugin_dir_url( __FILE__ );
		$this->Site = 'http://gqevu6bsiz.chicappa.jp/';
		$this->AuthorUrl = 'http://gqevu6bsiz.chicappa.jp/';
		$this->ltd = 'jcim';
		$this->Record = "js_css_include_manager";
		$this->Record_d = "js_css_include_manager" . '_donated';
		$this->Record_dw = "js_css_include_manager" . '_donated_width';
		$this->PageSlug = "js_css_include_manager";
		$this->PluginSlug = dirname( plugin_basename( __FILE__ ) );
		$this->ColumnCount = 6;
		$this->Nonces = array( "field" => $this->ltd . '_field' , "value" => $this->ltd . '_value' );
		$this->Schema = is_ssl() ? 'https://' : 'http://';
		$this->UPFN = 'Y';
		$this->DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
		$this->MsgQ = $this->ltd . '_msg';
		
		$this->PluginSetup();
		$this->FilterStart();
	}



	// PluginSetup
	function PluginSetup() {
		// load text domain
		load_plugin_textdomain( $this->ltd , false , $this->PluginSlug . '/languages' );

		// plugin links
		add_filter( 'plugin_action_links' , array( $this , 'plugin_action_links' ) , 10 , 2 );

		// add menu
		add_action( 'admin_menu' , array( $this , 'admin_menu' ) );

		// get data edit row
		add_action( 'wp_ajax_' . $this->ltd . '_get_edit_line' , array( $this , 'wp_ajax_' . $this->ltd . '_get_edit_line' ) );

		// data update
		add_action( 'admin_init' , array( $this , 'dataUpdate') );

		// data delete row
		add_action( 'wp_ajax_' . $this->ltd . '_delete_line' , array( $this , 'wp_ajax_' . $this->ltd . '_delete_line' ) );

		// data update row
		add_action( 'wp_ajax_' . $this->ltd . '_update_line' , array( $this , 'wp_ajax_' . $this->ltd . '_update_line' ) );

		// set donation toggle
		add_action( 'wp_ajax_' . $this->ltd . '_get_load_header' , array( $this , 'wp_ajax_' . $this->ltd . '_get_load_header' ) );

		// set donation toggle
		add_action( 'wp_ajax_' . $this->ltd . '_set_donation_toggle' , array( $this , 'wp_ajax_' . $this->ltd . '_set_donation_toggle' ) );
	}

	// PluginSetup
	function plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {
			$link = '<a href="' . self_admin_url( 'options-general.php?page=' . $this->PageSlug ) . '">' . __( 'Settings' ) . '</a>';
			$support_link = '<a href="http://wordpress.org/support/plugin/' . $this->PluginSlug . '" target="_blank">' . __( 'Support Forums' ) . '</a>';
			array_unshift( $links, $link , $support_link  );
		}
		return $links;
	}

	// PluginSetup
	function admin_menu() {
		add_options_page( $this->Name , $this->Name , 'administrator' , $this->PageSlug , array( $this , 'setting' ) );
	}




	// GetData
	function get_data() {
		$GetData = get_option( $this->Record );

		$Data = array();
		if( !empty( $GetData ) ) {
			$Data = $GetData;
		}

		return $Data;
	}

	// GetData
	function get_filting_data( $FiltSet ) {
		$Datas = $this->get_data();
		$Settings = $this->get_settings_conf();
		
		$DataFilt = array();
		if( !empty( $Datas ) ) {
			foreach( $Datas as $key => $data ) {

				// use = admin or normal
				// filetype = javascript or css
				// output =  wp_head or wp_footer
				// condition = is_user_logged_in() etc...
				// data_ver = legacy data identification
				if( !empty( $data["use"] ) && !empty( $data["filetype"] ) && !empty( $data["output"] ) && !empty( $data["location"] ) ) {
					if( $data["use"] == $FiltSet["use"] && $data["output"] == $FiltSet["output"] ) {

						if( !empty( $data["condition"] ) ) {
							$continue = $this->condition_check( $data );
							if( $continue )
								continue;
						}
						
						$IncludePath = strip_tags( $Settings["location"][$data["location"]["num"]]["location"] );
						if( empty( $data["data_ver"] ) && $data["location"]["num"] == 3 ) {
							$IncludePath = get_template_directory_uri() . '/';
						}
						$IncludeFile = $IncludePath . strip_tags( $data["location"]["name"] );

						if( $data["filetype"] == 1 ) {
							$DataFilt['js'][$key] = array( "file" => $IncludeFile , "output" => $data["output"] );
						} else if( $data["filetype"] == 2 ) {
							$DataFilt['css'][$key] = array( "file" => $IncludeFile , "output" => $data["output"] );
						}

					}
				}


			}
		}

		return $DataFilt;
		
	}




	// SettingPage
	function setting() {
		$this->display_msg();
		add_filter( 'admin_footer_text' , array( $this , 'layout_footer' ) );
		$this->DisplayDonation();
		include_once 'inc/setting.php';
	}




	// SetList
	function get_settings_conf() {
		$Conf["file_type"] = array( 1 => 'Javascript' , 2 => 'CSS' );
		$Conf["output"] = array( 1 => 'wp_head' , 2 => 'wp_footer' );
		$Conf["panel_type"] = array( 1 => __( 'Back-end (Admin screen)' , $this->ltd ) , 2 => __( 'Front-end (Site screen)' , $this->ltd ) );
		$Conf["location"] = $this->get_locations();
		$Conf["condition"] =  $this->get_conditions();

		return $Conf;
	}

	// SetList
	function get_conditions() {
		$conditions = array();
		$conditions[1] = array( "code" => "" , "desc" => __( 'No further condition' , $this->ltd  ) , "help_link" => "" );
		$conditions[2] = array(  "code" => "is_user_logged_in()" , "desc" => __( 'Only logged in user' , $this->ltd  ) , "help_link" => "http://codex.wordpress.org/Function_Reference/is_user_logged_in" );
		$conditions[3] = array(  "code" => "current_user_can( 'manage_options' )" , "desc" => __( 'Only manage option role have user' , $this->ltd  ) , "help_link" => "http://codex.wordpress.org/Function_Reference/current_user_can" );
		$conditions[4] = array(  "code" => "is_front_page()" , "desc" => __( 'Only Front page( Home )' , $this->ltd  ) , "help_link" => "http://codex.wordpress.org/Function_Reference/is_front_page" );
		
		$conditions = $this->add_condition( $conditions );
		
		return $conditions;
	}
	
	// SetList
	function add_condition( $conditions ) {
		$add_conditions = array();
		$add_conditions = apply_filters( 'jcim_condition' , $add_conditions );
		
		if( !empty( $add_conditions ) ) {
			foreach( $add_conditions as $key => $cond ) {
				$func = strip_tags( $cond["code"] );
				$val = strip_tags( $cond["val"] );
				$desc = strip_tags( $cond["desc"] );
				$help_link = esc_url( $cond["help_link"] );
				$code = $func . "()";
				if( !empty( $val ) ) {
					$code = $func . "( '$val' )";
				}
				$conditions[] = array( "code" => $code , "desc" => $desc , "help_link" => $help_link , "func" => $func , "val" => $val );
			}
		}
		
		return $conditions;
	}

	// SetList
	function get_locations() {
		$current_theme = wp_get_theme();
	
		$Location = array(
			1 => array(
				'name' => __( 'This Plugin Directory' , $this->ltd ),
				'location' => $this->Url
			),
			2 => array(
				'name' => __( 'Other Plugin Directory' , $this->ltd ),
				'location' => content_url().'/plugins/'
			),
			3 => array(
				'name' => __( 'The Active Theme\'s Directory' , $this->ltd ) .' <span class="description">(' . $current_theme->display( 'Name' ) . ')</span>',
				'location' => get_stylesheet_directory_uri().'/'
			),
			4 => array(
				'name' => __( 'Other Theme\'s Directory' , $this->ltd ),
				'location' => content_url().'/themes/'
			),
			5 => array(
				'name' => __( 'External File' , $this->ltd ),
				'location' => ''
			),
		);
	
		return $Location;
	}

	// SetList
	function wp_ajax_jcim_get_edit_line() {
		if( !empty( $_POST["action"] ) && $_POST["action"] == $this->ltd . '_get_edit_line' && check_ajax_referer( $this->Nonces["value"] , "nonce" ) ) {
			if( isset( $_POST["data"]["edit_id"] ) ) {

				$EditID = intval( $_POST["data"]["edit_id"] );
				$GetData = $this->get_data();
				
				if( !empty( $GetData[$EditID] ) ) {
					
					$Data = $GetData[$EditID];
					$Settings = $this->get_settings_conf();
					
?>
					<tr class="inline-edit-row quick-edit-row-post <?php if( !empty( $_POST["data"]["alternate"] ) && $_POST["data"]["alternate"] == "true" ) echo 'alternate'; ?>" id="data_inline_<?php echo $EditID; ?>">
						<td colspan="<?php echo $this->ColumnCount; ?>">

							<form class="jcim_form" method="post" action="<?php echo self_admin_url( 'options-general.php?page=' . $this->PageSlug ); ?>" name="update_row">
								<?php $Val = ''; if( !empty( $Data["data_ver"] ) ) $Val = $Data["data_ver"]; ?>
								<input type="hidden" name="data_ver" value="<?php echo $Val; ?>" />
								<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
								<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>

								<fieldset class="inline-edit-col-left">
									<div class="inline-edit-col">
	
										<label class="inline-edit-author">
											<span class="title"><?php _e( 'Panel Type' , $this->ltd ); ?></span>
											<select name="use">
												<?php foreach( $Settings["panel_type"] as $usenum => $usetype ) : ?>
													<?php $Selected = ''; ?>
													<?php if( !empty( $usetype ) ) : ?>
														<?php if( $usenum == strip_tags( $Data["use"] ) ) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $usenum; ?>" <?php echo $Selected; ?>><?php echo $usetype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</label>
	
										<label class="inline-edit-author">
											<span class="title"><?php _e( 'File Type' , $this->ltd ); ?></span>
											<select name="filetype">
												<?php foreach( $Settings["file_type"] as $filenum => $filetype ) : ?>
													<?php $Selected = ''; ?>
													<?php if( !empty( $filetype ) ) : ?>
														<?php if( $filenum == strip_tags( $Data["filetype"] ) ) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $filenum; ?>" <?php echo $Selected; ?>><?php echo $filetype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</label>
	
										<label class="inline-edit-author">
											<span class="title"><?php _e( 'Output' , $this->ltd ); ?></span>
											<select name="output">
												<?php foreach( $Settings["output"] as $outputnum => $outputtype ) : ?>
													<?php $Selected = ''; ?>
													<?php if( !empty( $outputtype ) ) : ?>
														<?php if( $outputnum == strip_tags( $Data["output"] ) ) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $outputnum; ?>" <?php echo $Selected; ?>><?php echo $outputtype; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</label>
	
										<label class="inline-edit-author">
											<span class="title"><?php _e( 'Condition' , $this->ltd ); ?></span>
											<select name="condition">
												<?php foreach( $Settings["condition"] as $condnum => $condtype ) : ?>
													<?php $Selected = ''; ?>
													<?php if( !empty( $condtype ) ) : ?>
														<?php if( !empty( $Data["condition"] ) && $condnum == strip_tags( $Data["condition"] ) ) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $condnum; ?>" <?php echo $Selected; ?>>
															<?php if( empty( $condtype["code"] ) ) : ?>
																<?php echo strip_tags( $condtype["desc"] ); ?>
															<?php else: ?>
																<?php echo strip_tags( $condtype["code"] ); ?>
															<?php endif; ?>
														</option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</label>
	
									</div>
								</fieldset>
	
								<fieldset class="inline-edit-col-right">
									<div class="inline-edit-col">
										<ul>
											<?php foreach($Settings["location"] as $location_num => $location_val) : ?>
												<?php $Checked = ""; ?>
												<?php $ClassDis = 'disabled'; ?>
												<?php $Disabled = 'disabled="disabled"'; ?>
												<?php $Value = ''; ?>
												<?php if( !empty( $location_val["name"] ) ) : ?>
													<?php if( $Data["location"]["num"] == $location_num ) : ?>
														<?php $Checked = 'checked="checked"'; ?>
														<?php $ClassDis = ''; ?>
														<?php $Disabled = ''; ?>
														<?php $Value = strip_tags( $Data["location"]["name"] ); ?>
													<?php endif; ?>
													<?php if( empty( $Data["data_ver"] ) && $location_num == 3 ) : ?>
														<?php $location_val = array( "name" => __('The Active Theme\'s Directory', $this->ltd ).' <span class="description">(' . get_template() . ')</span>' , "location" => get_template_directory_uri() . '/' ); ?>
													<?php endif; ?>
													<li>
														<label><input type="radio" class="location_radio" name="location_num" value="<?php echo $location_num; ?>" <?php echo $Checked; ?> /><?php echo $location_val["name"]; ?></label>
														<?php if( !empty( $location_val["location"] ) ) : ?>
															<code><?php echo $location_val["location"]; ?></code>
														<?php else: ?>
															<code>http://sample.com/sample.css or http://sample.com/sample.js</code>
														<?php endif; ?>
														<input type="text" name="location_name" class="large-text <?php echo $ClassDis; ?>" <?php echo $Disabled; ?> value="<?php echo $Value; ?>" />
													</li>
												<?php endif; ?>
											<?php endforeach; ?>
										</ul>
									</div>
								</fieldset>
	
								<p class="submit inline-edit-save">
									<a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft"><?php _e( 'Cancel' ); ?></a>
									<input accesskey="s" type="submit" class="button-primary save alignright" name="update" value="<?php _e( 'Update' ); ?>" />
									<span class="spinner"></span>
									<br class="clear" />
								</p>
							</form>

						</td>
					</tr>
<?php

				}

			}
		}
		die();
	}

	// SetList
	function get_list( $row_key , $alternate, $saved = false ) {
		$GetData = $this->get_data();
		
		if( isset( $row_key ) && !empty( $GetData[$row_key] ) ) {
			$Data = $GetData[$row_key];
			$Settings = $this->get_settings_conf();

			$TrClass = "";
			if( !empty( $alternate ) ) {
				$TrClass = "alternate";
			}
			if( !empty( $saved ) && $saved == 'saved' ) {
				$TrClass .= 'saved';
			}
			
			$use = intval( $Data["use"] );
			$filetype = intval( $Data["filetype"] );
			$output = intval( $Data["output"] );
			$condition = intval( $Data["condition"] );
			$location_num = intval( $Data["location"]["num"] );
			$location_name = strip_tags( $Data["location"]["name"] );
?>
			<tr class="<?php echo $TrClass; ?>" id="data_<?php echo $row_key; ?>">
				<td class="use">
					<?php echo $Settings["panel_type"][$use]; ?>
				</td>
				<td class="filetype">
					<?php echo $Settings["file_type"][$filetype]; ?>
				</td>
				<td class="output">
					<?php echo $Settings["output"][$output]; ?>
				</td>
				<td class="condition">
					<?php if( !empty( $condition ) ) : ?>
						<?php if( !empty( $Settings["condition"][$condition]["code"] ) ) : ?>
							<?php echo strip_tags( $Settings["condition"][$condition]["code"] ); ?>
						<?php else: ?>
							<?php if( !empty( $Settings["condition"][$condition]["desc"] ) ) : ?>
								<?php echo strip_tags( $Settings["condition"][$condition]["desc"] ); ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php else : ?>
						<?php echo $Settings["condition"][1]["desc"]; ?>
					<?php endif; ?>
				</td>
				<td class="location">
					<?php $FileUrl = $Settings["location"][$location_num]["location"] . $location_name; ?>
					<?php if( empty( $val["data_ver"] ) && $location_num == 3 ) : ?>
						<?php $FileUrl = get_template_directory_uri() . '/' . $location_name; ?>
					<?php endif; ?>
					<p><a href="<?php echo $FileUrl; ?>" target="_blank"><?php echo esc_html( $FileUrl ); ?></a></p>
					<?php if( !empty( $FileUrl ) ) : ?>
						<div class="spinner"></div>
						<code></code>

<script>
jQuery(document).ready(function($) {

	$("tr#data_<?php echo $row_key; ?>").children("td.location").find(".spinner").show();

	var PostData = {
		action: 'jcim_get_load_header',
		nonce: js_css_include_manager.nonce,
		data: {
			file_url: '<?php echo $FileUrl; ?>',
			load_id: '<?php echo $row_key; ?>'
		}
	};
	$.post( js_css_include_manager.ajax_url , PostData , function( response ) {
		if( typeof( response ) == 'object' && response.success ) {
			$("tr#data_<?php echo $row_key; ?>").children("td.location").find(".spinner").hide();
			if( response.data.code ) {
				$("tr#data_<?php echo $row_key; ?>").children("td.location").find("code").html( response.data.code );
			} else {
				$("tr#data_<?php echo $row_key; ?>").children("td.location").find("code").hide();
			}
		}
	});
});
</script>

					<?php endif; ?>
				</td>
				<td class="operation">
					<a class="edit button-primary" href="#"><?php _e( 'Edit' ); ?></a>
					<a class="delete button" href="#"><?php _e( 'Delete' ); ?></a>
					<span class="spinner"></span>
				</td>
			</tr>
<?php
		}
	}

	// SetList
	function wp_ajax_jcim_get_load_header() {
		if( !empty( $_POST["action"] ) && $_POST["action"] == $this->ltd . '_get_load_header' && check_ajax_referer( $this->Nonces["value"] , "nonce" ) ) {
			if( isset( $_POST["data"]["file_url"] ) ) {
				
				$response = wp_remote_get( esc_url( $_POST["data"]["file_url"] ) );
				$code = "";

				if ( is_wp_error( $response ) ) {
					$code = 'No Header';
				} elseif( 200 != wp_remote_retrieve_response_code( $response ) ) {
					$code = wp_remote_retrieve_response_code( $response );
				}
				
				wp_send_json_success( array( "code" => $code ) );
				
			}
		}
		die();
	}

	// SetList
	function condition_check( $data ) {
		$Settings = $this->get_settings_conf();
		$continue = false;

		if( $data["condition"] != 1 ) {
			if( $data["condition"] == 2 ) {
				if ( !is_user_logged_in() ) $continue = true;
			} elseif( $data["condition"] == 3 ) {
				if ( !current_user_can( 'manage_options' ) ) $continue = true;
			} elseif( $data["condition"] == 4 ) {
				if ( !is_front_page() ) $continue = true;
			} else {
				
				if( !empty( $Settings["condition"][$data["condition"]]["func"] ) ) {
					
					$my_cond = $Settings["condition"][$data["condition"]]["func"];
					$my_cond_val = "";
					if( !empty( $Settings["condition"][$data["condition"]]["val"] ) ) {
						$my_cond_val = $Settings["condition"][$data["condition"]]["val"];
					}
					if( !$my_cond( $my_cond_val ) ) {
						$continue = true;
					}
				}
			}
		}
		
		return $continue;
	}




	// DataUpdate
	function dataUpdate() {

		$RecordField = false;
		
		if( !empty( $_POST["record_field"] ) ) {
			$RecordField = strip_tags( $_POST["record_field"] );
		}

		if( !empty( $RecordField ) && $RecordField == $this->Record ) {
			if( !empty( $_POST["create"] ) ) {
				$this->create();
			}
			if( !empty( $_POST["donate_key"] ) ) {
				$this->DonatingCheck();
			}
		}
	}

	// DataUpdate
	function DonatingCheck() {
		$Update = $this->update_validate();

		if( !empty( $Update ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			if( !empty( $_POST["donate_key"] ) ) {
				$SubmitKey = md5( strip_tags( $_POST["donate_key"] ) );
				if( $this->DonateKey == $SubmitKey ) {
					update_option( $this->Record_d , $SubmitKey );
					wp_redirect( add_query_arg( $this->MsgQ , 'donated' ) );
					exit;
				}
			}
		}

	}

	// DataUpdate
	function update_validate() {
		$Update = array();

		if( !empty( $_POST[$this->UPFN] ) ) {
			$UPFN = strip_tags( $_POST[$this->UPFN] );
			if( $UPFN == $this->UPFN ) {
				$Update["UPFN"] = strip_tags( $_POST[$this->UPFN] );
			}
		}

		return $Update;
	}

	// DataUpdate
	function create() {
		$Update = $this->update_validate();
		if( !empty( $Update ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {

			$PostData = $_POST["create"];
			$data_ver = 0;

			if( !empty( $PostData["location"]["num"] ) ) {

				$location_num = intval( $PostData["location"]["num"] );
				$Update["location"] = array( "num" => $location_num , "name" => strip_tags( $PostData["location"]["name"][$location_num] ) );

				if( !empty( $Update["location"]["name"] ) ) {
					unset( $Update["UPFN"] );

					if( !empty( $PostData["data_ver"] ) ) {
						$data_ver = intval( $PostData["data_ver"]);
					}
					$Update["data_ver"] = $data_ver;
	
					$arr = array( "use" , "filetype" , "output" , "condition" );
					foreach( $arr as $key ) {
						$Update[$key] = intval( $PostData[$key] );
					}
					
					$UpdateData = $this->get_data();
					$UpdateData[] = $Update;
					update_option( $this->Record , $UpdateData );
					wp_redirect( add_query_arg( $this->MsgQ , 'update' ) );
					exit;

				}

			}

		}
	}

	// DataUpdate
	function wp_ajax_jcim_delete_line() {
		if( !empty( $_POST["action"] ) && $_POST["action"] == $this->ltd . '_delete_line' && check_ajax_referer( $this->Nonces["value"] , "nonce" ) ) {
			if( isset( $_POST["data"]["delete_id"] ) ) {

				$GetData = $this->get_data();
				$id = intval( $_POST["data"]["delete_id"] );
				unset( $GetData[$id] );

				update_option( $this->Record , $GetData );
				wp_send_json_success();

			}
		}
		die();
	}

	// DataUpdate
	function wp_ajax_jcim_update_line() {
		if( !empty( $_POST["action"] ) && $_POST["action"] == $this->ltd . '_update_line' && check_ajax_referer( $this->Nonces["value"] , "nonce" ) ) {
			if( isset( $_POST["data"]["update_id"] ) ) {

				$PostData = $_POST["data"];
				$data_ver = 0;

				if( !empty( $PostData["location_num"] ) ) {
					$location_num = intval( $PostData["location_num"] );
					$Update["location"] = array( "num" => $location_num , "name" => strip_tags( $PostData["location_name"] ) );
					if( !empty( $Update["location"]["name"] ) ) {
						
						if( !empty( $PostData["data_ver"] ) ) {
							$data_ver = intval( $PostData["data_ver"]);
						}
						$Update["data_ver"] = $data_ver;
						
						$arr = array( "use" , "filetype" , "output" , "condition" );
						foreach( $arr as $key ) {
							$Update[$key] = intval( $PostData[$key] );
						}

						$UpdateData = $this->get_data();
						$UpdateData[$PostData["update_id"]] = $Update;
						
						update_option( $this->Record , $UpdateData );

						echo $this->get_list( $PostData["update_id"] , false , true );

					}

				}

			}
		}
		die();
	}

	// DataUpdate
	function wp_ajax_jcim_set_donation_toggle() {
		update_option( $this->Record_dw , intval( $_POST["f"] ) );
		die();
	}




	// FilterStart
	function FilterStart() {

		add_action( 'admin_enqueue_scripts' , array( $this , 'print_admin_head' ) );
		add_action( 'admin_footer' , array( $this , 'print_admin_foot' ) );

		add_action( 'wp_enqueue_scripts' , array( $this , 'print_front_head' ) );
		add_action( 'get_footer' , array( $this , 'print_front_foot' ) );

	}

	// FilterStart
	function print_admin_head() {
		$this->do_include( array( "use" => 1 , "output" => 1 ) );
	}

	// FilterStart
	function print_admin_foot() {
		$this->do_include( array( "use" => 1 , "output" => 2 ) );
	}

	// FilterStart
	function print_front_head() {
		$this->do_include( array( "use" => 2 , "output" => 1 ) );
	}

	// FilterStart
	function print_front_foot() {
		$this->do_include( array( "use" => 2 , "output" => 2 ) );
	}

	// FilterStart
	function do_include( $Defaults ) {
		$GetData = $this->get_filting_data( $Defaults );
		if( !empty( $GetData ) ) {
			$this->print_js_css( $GetData );
		}
	}

	// FilterStart
	function print_js_css( $Data ) {

		if( !empty( $Data ) ) {
			foreach( $Data as $file_type => $manage_sets ) {
				if( !empty( $manage_sets ) ) {
					$current_date = date( 'Ymd' , current_time( "timestamp" ) );

					foreach( $manage_sets as $key => $manage_set ) {

						if( $file_type == 'js' ) {
							if( $manage_set["output"] == 1 ) {
								wp_enqueue_script($this->PageSlug . '-' . $key , $manage_set["file"] , array( 'jquery' ) , $current_date );
							} elseif( $manage_set["output"] == 2 ) {
								wp_enqueue_script( $this->PageSlug . '-' . $key , $manage_set["file"] , array( 'jquery' ) , $current_date , true );
							}
						} elseif( $file_type == 'css' ) {
							wp_enqueue_style( $this->PageSlug . '-' . $key , $manage_set["file"] , array() , $current_date );
						}

					}

				}
			}
		}

	}


	// FilterStart
	function display_msg() {
		if( !empty( $_GET[$this->MsgQ] ) ) {
			$msg = strip_tags(  $_GET[$this->MsgQ] );
			if( $msg == 'update' or $msg == 'delete' ) {
				$this->Msg .= '<div class="updated"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
			} elseif( $msg == 'donated' ) {
				$this->Msg .= '<div class="updated"><p><strong>' . __( 'Thank you for your donation.' , $this->ltd ) . '</strong></p></div>';
			}
		}
	}

	// FilterStart
	function layout_footer( $text ) {
		$text = '<img src="' . $this->Schema . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=18" width="18" /> Plugin developer : <a href="' . $this->AuthorUrl . '?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">gqevu6bsiz</a>';
		return $text;
	}

	// FilterStart
	function DisplayDonation() {
		$donation = get_option( $this->Record_d );
		if( $this->DonateKey != $donation ) {
			$this->Msg .= '<div class="error"><p><strong>' . __( 'Thank you for considering donate.' , $this->ltd ) . '</strong></p></div>';
		}
	}


}

$jcim = new JS_CSS_include_manager();

?>