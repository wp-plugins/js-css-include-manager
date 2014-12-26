<?php

if ( !class_exists( 'Jcim_Config' ) ) :

class Jcim_Config
{

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'setup_config' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_record' ) );
		add_action( 'init' , array( $this , 'setup_site_env' ) );
		add_action( 'init' , array( $this , 'setup_current_env' ) );
		add_action( 'init' , array( $this , 'setup_current_user' ) );
		add_action( 'init' , array( $this , 'setup_links' ) );
		add_action( 'init' , array( $this , 'setup_third_party' ) );
		
	}

	function setup_config() {
		
		global $Jcim;
		
		$Jcim->Plugin['plugin_slug']  = 'js-css-include-manager';
		$Jcim->Plugin['dir']          = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$Jcim->Plugin['name']         = 'Js Css Include Manager';
		$Jcim->Plugin['page_slug']    = str_replace( '-' , '_' , $Jcim->Plugin['plugin_slug'] );
		$Jcim->Plugin['url']          = plugin_dir_url( dirname( __FILE__ ) );
		$Jcim->Plugin['ltd']          = 'jcim';
		$Jcim->Plugin['nonces']       = array( 'field' => $Jcim->Plugin['ltd'] . '_field' , 'value' => $Jcim->Plugin['ltd'] . '_value' );
		$Jcim->Plugin['UPFN']         = 'Y';
		$Jcim->Plugin['form']         = array( 'field' => $Jcim->Plugin['ltd'] . '_settings' );
		$Jcim->Plugin['msg_notice']   = $Jcim->Plugin['ltd'] . '_msg';
		$Jcim->Plugin['default_role'] = array( 'child' => 'manage_options' , 'network' => 'manage_network' );

		$Jcim->Plugin['dir_admin_assets'] = $Jcim->Plugin['url'] . trailingslashit( 'admin' ) . trailingslashit( 'assets' );
		
	}

	function setup_record() {
		
		global $Jcim;
		
		$Jcim->Plugin['record']['setting'] = $Jcim->Plugin['page_slug'];
		$Jcim->Plugin['record']['other'] = $Jcim->Plugin['ltd'] . '_other';
		
	}
	
	function setup_site_env() {
		
		global $Jcim;

		$Jcim->Current['multisite'] = is_multisite();
		$Jcim->Current['blog_id'] = get_current_blog_id();

		$Jcim->Current['main_blog'] = false;

		if( $Jcim->Current['blog_id'] == 1 ) {

			$Jcim->Current['main_blog'] = true;

		}
		
	}

	function setup_current_env() {
		
		global $Jcim;
		
		$Jcim->Current['admin']         = is_admin();
		$Jcim->Current['network_admin'] = is_network_admin();

		$Jcim->Current['ajax']          = false;

		if( defined( 'DOING_AJAX' ) )
			$Jcim->Current['ajax'] = true;
			
		$Jcim->Current['schema'] = is_ssl() ? 'https://' : 'http://';
		
	}
	
	function setup_current_user() {
		
		global $Jcim;
		
		$Jcim->Current['user_login']    = is_user_logged_in();

		$Jcim->Current['user_role']     = false;

		$User = wp_get_current_user();

		if( !empty( $User->roles ) ) {

			$current_roles = $User->roles;

			foreach( $current_roles as $role ) {

				$Jcim->Current['user_role'] = $role;
				break;

			}

		}

		$Jcim->Current['superadmin']    = false;

		if( $Jcim->Current['multisite'] )
			$Jcim->Current['superadmin'] = is_super_admin();

	}
	
	function setup_links() {
		
		global $Jcim;
		
		$Jcim->Plugin['links']['author'] = 'http://gqevu6bsiz.chicappa.jp/';
		$Jcim->Plugin['links']['forum'] = 'http://wordpress.org/support/plugin/' . $Jcim->Plugin['plugin_slug'];
		$Jcim->Plugin['links']['review'] = 'http://wordpress.org/support/view/plugin-reviews/' . $Jcim->Plugin['plugin_slug'];
		$Jcim->Plugin['links']['profile'] = 'http://profiles.wordpress.org/gqevu6bsiz';
		
		if( $Jcim->Current['multisite'] ) {

			$Jcim->Plugin['links']['setting'] = network_admin_url( 'admin.php?page=' . $Jcim->Plugin['page_slug'] );

		} else {

			$Jcim->Plugin['links']['setting'] = admin_url( 'options-general.php?page=' . $Jcim->Plugin['page_slug'] );

		}
		
	}

	function setup_third_party() {
		
		global $Jcim;

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$check_plugins = array();
		
		if( !empty( $check_plugins ) ) {
			foreach( $check_plugins as $name => $base_name ) {
				if( is_plugin_active( $base_name ) )
					$Jcim->ThirdParty[$name] = true;
			}
		}

	}

	function get_all_user_roles() {

		global $Jcim;
		global $wp_roles;

		$UserRole = array();
		$all_user_roles = $wp_roles->roles;

		foreach ( $all_user_roles as $role => $user ) {

			$user['label'] = translate_user_role( $user['name'] );
			$UserRole[$role] = $user;

		}
		
		if( !empty( $Jcim->Current['multisite'] ) && !empty( $Jcim->Current['network_admin'] ) && !empty( $Jcim->Current['superadmin'] ) ) {
			
			$add_caps = array( 'manage_network' , 'manage_network_users' , 'manage_network_themes' , 'manage_network_plugins' , 'manage_network_options' );

			foreach( $add_caps as $cap ) {

				$UserRole[$Jcim->Current['user_role']]['capabilities'][$cap] = 1;

			}
			
		}

		return $UserRole;

	}

	function get_file_types() {

		$file_types = array( 1 => 'Javascript' , 2 => 'CSS' );
		
		return $file_types;

	}

	function get_output_types() {

		$output_types = array( 1 => 'wp_head' , 2 => 'wp_footer' );
		
		return $output_types;

	}

	function get_screen_types() {

		global $Jcim;

		$screen_types = array(
			1 => __( 'Back-end (Admin screen)' , $Jcim->Plugin['ltd'] ),
			2 => __( 'Front-end (Site screen)' , $Jcim->Plugin['ltd'] ),
		);
		
		return $screen_types;

	}

	function get_locations() {

		global $Jcim;

		$current_theme = wp_get_theme();

		$locations = array(
			1 => array(
				'name' => __( 'This Plugin Directory' , $Jcim->Plugin['ltd'] ),
				'location' => $Jcim->Plugin['url']
			),
			2 => array(
				'name' => __( 'Other Plugin Directory' ,$Jcim->Plugin['ltd'] ),
				'location' => trailingslashit( plugins_url() )
			),
			3 => array(
				'name' => sprintf( '%1$s <span class="description">(%2$s)</span>' , __( 'Actived Theme Directory' , $Jcim->Plugin['ltd'] ) , $current_theme->display( 'Name' ) ),
				'location' => trailingslashit( get_stylesheet_directory_uri() )
			),
			4 => array(
				'name' => __( 'Other Theme Directory' , $Jcim->Plugin['ltd'] ),
				'location' => trailingslashit( content_url() ) . trailingslashit( 'themes' )
			),
			5 => array(
				'name' => __( 'External File' , $Jcim->Plugin['ltd'] ),
				'location' => ''
			),
		);
		
		return $locations;

	}

	function get_conditions() {

		global $Jcim;

		$current_theme = wp_get_theme();

		$conditions = array(
			1 => array(
				'func' => '',
				'func_value' => '',
				'desc' => __( 'No further condition' , $Jcim->Plugin['ltd']  ),
				'help_link' => ''
			),
			2 => array(
				'func' => 'is_user_logged_in',
				'func_value' => '',
				'desc' => __( 'Only logged in user' , $Jcim->Plugin['ltd']  ),
				'help_link' => 'http://codex.wordpress.org/Function_Reference/is_user_logged_in'
			),
			3 => array(
				'func' => 'current_user_can',
				'func_value' => 'manage_options',
				'desc' => __( 'Only manage option role have user' , $Jcim->Plugin['ltd']  ),
				'help_link' => 'http://codex.wordpress.org/Function_Reference/current_user_can'
			),
			4 => array(
				'func' => 'is_front_page',
				'func_value' => '',
				'desc' => __( 'Only Front page( Home )' , $Jcim->Plugin['ltd']  ),
				'help_link' => 'http://codex.wordpress.org/Function_Reference/is_front_page'
			),
		);
		
		$conditions = $this->add_condition( $conditions );
		
		foreach( $conditions as $key => $condition ) {
			
			$func_code = false;
			$func_value = false;

			if( !empty( $condition['func'] ) ) {

				$func_code = $condition['func'];
				$func_value = '()';
				
				if( !empty( $condition['func_value'] ) )
					$func_value = sprintf( '( "%s" )' , $condition['func_value'] );

			}

			$conditions[$key]['code'] = $condition['func'] . $func_value;

		}
		
		return $conditions;

	}
	
	function add_condition( $conditions ) {
		
		$add_conditions = array();
		$add_conditions = apply_filters( 'jcim_condition' , $add_conditions );
		
		if( !empty( $add_conditions ) ) {
			
			foreach( $add_conditions as $key => $condition ) {
				
				if( !empty( $condition['code'] ) ) {

					$func = strip_tags( $condition['code'] );

					if( function_exists( $func ) ) {
						
						$func_val = $desc = $help_link = false;
						
						if( !empty( $condition['val'] ) )
							$func_val = strip_tags( $condition['val'] );

						if( !empty( $condition['desc'] ) )
							$desc = strip_tags( $condition['desc'] );

						if( !empty( $condition['help_link'] ) )
							$help_link = esc_url( $condition['help_link'] );

						$conditions[] =array( 'func' => $func , 'func_value' => $func_val , 'desc' => $desc , 'help_link' => $help_link );
						
					}
					
				}
				
			}
			
		}
		
		return $conditions;
		
	}
	
}

endif;
