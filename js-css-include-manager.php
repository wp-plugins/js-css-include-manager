<?php
/*
Plugin Name: Js Css Include Manager
Description: This plug-in is a will clean the file management. You can only manage the screen. You can also only site the screen.
Plugin URI: http://wordpress.org/extend/plugins/js-css-include-manager/
Version: 1.4.3
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=jcim&utm_campaign=1_4_3
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



if ( !class_exists( 'JS_CSS_include_manager' ) ) :

class JS_CSS_include_manager
{

	var	$Ver = '1.4.3';

	var $Plugin = array();
	var $Current = array();
	var $ThirdParty = array();

	var $ClassConfig;
	var $ClassData;
	var $ClassManager;
	var $ClassInfo;

	function __construct() {

		$inc_path = plugin_dir_path( __FILE__ );

		include_once $inc_path . 'inc/class-config.php';
		include_once $inc_path . 'inc/class-data.php';
		include_once $inc_path . 'inc/class-manager.php';
		include_once $inc_path . 'inc/class-plugin-info.php';

		$this->ClassConfig = new Jcim_Config();
		$this->ClassData = new Jcim_Data();
		$this->ClassManager = new Jcim_Manager();
		$this->ClassInfo = new Jcim_Plugin_Info();

		add_action( 'plugins_loaded' , array( $this , 'init' ) , 100 );

	}

	function init() {
		
		load_plugin_textdomain( $this->Plugin['ltd'] , false , $this->Plugin['plugin_slug'] . '/languages' );
		
		add_action( 'wp_loaded' , array( $this , 'FilterStart' ) );

	}



	// SetList
	function fields_setting( $mode = 'add' , $field = false , $val = false , $key = false ) {
		
		if( !empty( $mode ) && !empty( $field ) ) {
			
			$f_name = sprintf( 'data[%1$s][%2$s]' , $mode , $field );

			if( $mode == 'update' )
				$f_name = sprintf( 'data[%1$s][%2$s][%3$s]' , $mode , $key , $field );

			$f_id = sprintf( '%1$s_%2$s' , $mode , $field );

			if( $mode == 'update' )
				$f_id = sprintf( '%1$s_%2$s_%3$s' , $mode , $key , $field );
			
			if( $field == 'use' ) {

				printf( '<select name="%1$s" id="%2$s">' , $f_name , $f_id );

				$screens = $this->ClassConfig->get_screen_types();
				
				foreach( $screens as $key => $screen_name ) {
					
					printf( '<option value="%1$s" %3$s>%2$s</option>' , $key , $screen_name , selected( $val , $key , false ) );
					
				}
				
				echo '</select>';

			} elseif( $field == 'filetype' ) {

				printf( '<select name="%1$s" id="%2$s">' , $f_name , $f_id );

				$file_types = $this->ClassConfig->get_file_types();
				
				foreach( $file_types as $key => $file_name ) {
					
					printf( '<option value="%1$s" %3$s>%2$s</option>' , $key , $file_name , selected( $val , $key , false ) );
					
				}
				
				echo '</select>';

			} elseif( $field == 'output' ) {

				printf( '<select name="%1$s" id="%2$s">' , $f_name , $f_id );

				$outputs = $this->ClassConfig->get_output_types();
				
				foreach( $outputs as $key => $output_name ) {
					
					printf( '<option value="%1$s" %3$s>%2$s</option>' , $key , $output_name , selected( $val , $key , false ) );
					
				}
				
				echo '</select>';

			} elseif( $field == 'condition' ) {

				printf( '<select name="%1$s" id="%2$s">' , $f_name , $f_id );

				$conditions = $this->ClassConfig->get_conditions();
				
				foreach( $conditions as $key => $condition ) {
					
					$code = $condition['code'];
					
					if( empty( $code ) )
						$code = $condition['desc'];
					
					if( !empty( $condition ) )
						printf( '<option value="%1$s" %3$s>%2$s</option>' , $key , $code , selected( $val , $key , false ) );
					
				}
				
				echo '</select>';
				
				printf( '<p><a href="javascript:void(0);" class="condition_desc_show button button-secondary">%1$s</a></p>' , __( 'Description of condition is here' , $this->Plugin['ltd'] ) );
				
				echo '<ul class="condition_desc">';

				foreach( $conditions as $key => $condition ) {
					
					if( !empty( $condition['code'] ) && !empty( $condition['help_link'] ) )
						printf( '<li><code><a href="%2$s" target="_blank">%1$s</a></code> <span class="description">%3$s</span></li>' , $condition['code'] , $condition["help_link"] , $condition['desc'] );

				}

				echo '</ul>';

				printf( '<p><a href="javascript:void(0);" class="condition_add_desc_show button button-secondary">%1$s</a></p>' , __( 'If you want to add conditions.' , $this->Plugin['ltd'] ) );
				
				echo '<div class="condition_add_desc">';

				printf( '<p>%s</p>' , __( 'You will be able to add the condition.' , $this->Plugin['ltd'] ) );
				printf( '<p>%s</p>' , __( 'Please refer to the <strong>readme.txt</strong> for more information.' , $this->Plugin['ltd'] ) );
				
				echo '<code>';
				echo esc_html( 'add_filter( "jcim_condition" , "example_my_conditions" );' );
				echo '</code>';
				
				echo '</div>';

			} elseif( $field == 'location' ) {

				echo '<ul>';

				$locations = $this->ClassConfig->get_locations();
				
				foreach( $locations as $key => $location ) {
					
					if( $mode != 'add' )
						$location = $this->convert_location( $key , $val['ver'] , $location );
					
					$num_field = sprintf( '<label><input type="radio" class="location_radio" name="%2$s[num]" value="%1$s" %4$s /> %3$s</label>' , $key , $f_name , $location['name'] , checked( $key , $val['num'] , false ) );

					if( empty( $location['location'] ) )
						$location['location'] = 'http://example.com/example.css or http://example.com/example.js';

					$location_name = '';
					if( !empty( $val['name'] ) && $key == $val['num'] )
						$location_name = $val['name'];
						
					$disabled = true;
					if( !empty( $val['num'] ) && $key == $val['num'] )
						$disabled = false;
					
					$addclass = '';
					if( !empty( $disabled ) )
						$addclass = 'disabled';

					$name_field = sprintf( '<code>%1$s</code><input type="text" name="%2$s[name]" class="large-text location_name %5$s" value="%3$s" %4$s />' , $location['location'] , $f_name , $location_name , disabled( $disabled , true , false ) , $addclass );
					
					printf( '<li>%1$s %2$s</li>' , $num_field , $name_field );
					
				}
				
				echo '</ul>';

			}

		}
		
	}

	// SetList
	function convert_location( $location_id , $ver , $location ) {
		
		if( $location_id == 3 && empty( $ver ) ) {
			
			$parent_theme = wp_get_theme( get_template() );

			$location['name'] = sprintf( '%1$s <span class="description">(%2$s)</span>' , __( 'Actived Theme Directory' , $this->Plugin['ltd'] ) , $parent_theme->display( 'Name' ) );
			$location['location'] = trailingslashit( get_template_directory_uri() );
			
		}

		return $location;

	}
	



	// FilterStart
	function FilterStart() {

		if( !$this->Current['network_admin'] && !$this->Current['ajax'] ) {
			
			$Data = $this->ClassData->get_current_data( $this->Current['admin'] );
			
			if( !empty( $Data ) ) {
				
				if( $this->Current['admin'] ) {

					add_action( 'admin_enqueue_scripts' , array( $this , 'print_admin_head' ) );
					add_action( 'admin_footer' , array( $this , 'print_admin_foot' ) );
					
				} else {

					add_action( 'wp_enqueue_scripts' , array( $this , 'print_front_head' ) );
					add_action( 'get_footer' , array( $this , 'print_front_foot' ) );
					
				}
				
			}

		}
		
	}
	
	// FilterStart
	function print_admin_head() {

		$this->do_include( array( 'use' => 1 , 'output' => 1 ) );

	}

	// FilterStart
	function print_admin_foot() {

		$this->do_include( array( 'use' => 1 , 'output' => 2 ) );

	}

	// FilterStart
	function print_front_head() {

		$this->do_include( array( 'use' => 2 , 'output' => 1 ) );

	}

	// FilterStart
	function print_front_foot() {

		$this->do_include( array( 'use' => 2 , 'output' => 2 ) );

	}

	// FilterStart
	function do_include( $out_types ) {
		
		$GetData = $this->get_filting_data( $out_types );

		if( !empty( $GetData ) )
			$this->do_print_js_css( $GetData );

	}

	// FilterStart
	function get_filting_data( $out_types ) {

		$Data = $this->ClassData->get_current_data( $this->Current['admin'] );
		$FilterData = array();

		$locations = $this->ClassConfig->get_locations();
		
		if( !empty( $Data ) ) {
			
			foreach( $Data as $key => $setting ) {
				
				if( $setting['output'] == $out_types['output'] ) {

					if( $setting['condition'] != 1 ) {
						
						$continue = $this->condition_check( $setting );
						
						if( !empty( $continue ) )
							continue;

					}

					$location_id = intval( $setting['location']['num'] );
					$location = $this->convert_location( $location_id , $setting['data_ver'] , $locations[$location_id] );
					
					$request_file = $location['location'] . strip_tags( $setting['location']['name'] );
					
					if( $setting['filetype'] == 1 ) {
						
						$FilterData['js'][$key] = array( 'file' => $request_file , 'output' => $setting['output'] );
						
					} elseif( $setting['filetype'] == 2 ) {
						
						$FilterData['css'][$key] = array( 'file' => $request_file , 'output' => $setting['output'] );
						
					}
					
				}
				
			}
			
		}

		return $FilterData;
		
	}
	
	// FilterStart
	function condition_check( $setting ) {
		
		$conditions = $this->ClassConfig->get_conditions();
		$cond_id = $setting['condition'];
		$continue = false;

		$func = false;
		
		if( !empty( $conditions[$cond_id]['func'] ) )
			$func = $conditions[$cond_id]['func'];
		
		if( function_exists( $func ) ) {
			
			$func_value = false;

			if( !empty( $conditions[$cond_id]['func_value'] ) )
				$func_value = $conditions[$cond_id]['func_value'];

			if( !$func( $func_value ) ) {
				
				$continue = true;

			}

		
		}
		
		return $continue;

	}
	
	// FilterStart
	function do_print_js_css( $Data ) {
		
		if( empty( $Data ) )
			return false;
		
		$current_date = date( 'Ymd' , current_time( 'timestamp' ) );
		
		foreach( $Data as $filetype => $settings ) {
			
			foreach( $settings as $key => $setting ) {
				
				$handle = $this->Plugin['page_slug'] . '-' . $key;

				if( $filetype == 'js' ) {
					
					if( $setting['output'] == 1 ) {
						
						wp_enqueue_script( $handle , $setting['file'] , array() , $current_date );
						
					} elseif( $setting['output'] == 2 ) {
						
						wp_enqueue_script( $handle , $setting['file'] , array() , $current_date , true );
						
					}
					
				} elseif( $filetype == 'css' ) {
					
					wp_enqueue_style( $handle , $setting['file'] , array() , $current_date );

				}
				
			}
			
		}

	}

}

$GLOBALS['Jcim'] = new JS_CSS_include_manager();

endif;
?>