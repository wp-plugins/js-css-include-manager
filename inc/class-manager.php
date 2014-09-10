<?php

if ( !class_exists( 'Jcim_Manager' ) ) :

class Jcim_Manager
{

	var $is_manager = false;
	
	function __construct() {
		
		if( is_admin() )
			add_action( 'init' , array( $this , 'set_manager' ) , 20 );
			add_action( 'init' , array( $this , 'init' ) , 20 );
		
	}

	function get_manager_user_role() {

		global $Jcim;

		$cap = false;

		if( is_multisite() ) {

			$cap = $Jcim->Plugin['default_role']['network'];

		} else {

			$cap = $Jcim->Plugin['default_role']['child'];

		}
		
		$other_data = $Jcim->ClassData->get_data_others();
		if( !empty( $other_data['capability'] ) )
			$cap = strip_tags( $other_data['capability'] );
		
		return $cap;

	}

	function set_manager() {
		
		$cap = $this->get_manager_user_role();
		if( current_user_can( $cap ) )
			$this->is_manager = true;
		
	}

	function init() {
		
		global $Jcim;
		
		if( $Jcim->Current['admin'] && $this->is_manager && !$Jcim->Current['ajax'] ) {
			
			$base_plugin = trailingslashit( $Jcim->Plugin['plugin_slug'] ) . $Jcim->Plugin['plugin_slug'] . '.php';
			
			if( $Jcim->Current['multisite'] ) {

				add_filter( 'network_admin_plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'network_admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'network_admin_notices' , array( $this , 'update_notice' ) );

			} else {

				add_filter( 'plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'admin_notices' , array( $this , 'update_notice' ) );

			}
			
			add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );

		}
		
		if( $Jcim->Current['admin'] && $this->is_manager && $Jcim->Current['ajax'] ) {
			
			add_action( 'wp_ajax_' . $Jcim->Plugin['ltd'] . '_get_load_header' , array( $this , $Jcim->Plugin['ltd'] . '_get_load_header' ) );
			
		}
		
	}

	function plugin_action_links( $links ) {

		global $Jcim;
		
		$link_setting = sprintf( '<a href="%1$s">%2$s</a>' , $Jcim->Plugin['links']['setting'] , __( 'Settings' ) );
		$link_support = sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , $Jcim->Plugin['links']['forum'] , __( 'Support Forums' ) );

		array_unshift( $links , $link_setting, $link_support );

		return $links;

	}

	function admin_menu() {
		
		global $Jcim;

		$cap = $this->get_manager_user_role();

		if( $Jcim->Current['multisite'] ) {

			add_menu_page( $Jcim->Plugin['name'] , $Jcim->Plugin['name'] , $cap , $Jcim->Plugin['page_slug'] , array( $this , 'views') );

		} else {
	
			add_options_page( $Jcim->Plugin['name'] , $Jcim->Plugin['name'] , $cap , $Jcim->Plugin['page_slug'] , array( $this , 'views' ) );

		}

	}

	function is_settings_page() {
		
		global $plugin_page;
		global $Jcim;
		
		$is_settings_page = false;
		$setting_pages = array( $Jcim->Plugin['page_slug'] );
		
		if( in_array( $plugin_page , $setting_pages ) )
			$is_settings_page = true;
		
		return $is_settings_page;
		
	}

	function admin_print_scripts() {
		
		global $plugin_page;
		global $wp_version;
		global $Jcim;
		
		if( $this->is_settings_page() ) {
			
			$ReadedJs = array( 'jquery' , 'jquery-ui-sortable' , 'thickbox' );
			wp_enqueue_script( $Jcim->Plugin['page_slug'] ,  $Jcim->Plugin['url'] . $Jcim->Plugin['ltd'] . '.js', $ReadedJs , $Jcim->Ver );
			add_thickbox();
			
			wp_enqueue_style( $Jcim->Plugin['page_slug'] , $Jcim->Plugin['url'] . $Jcim->Plugin['ltd'] . '.css', array() , $Jcim->Ver );
			if( version_compare( $wp_version , '3.8' , '<' ) )
				wp_enqueue_style( $Jcim->Plugin['page_slug'] . '-37' , $Jcim->Plugin['url'] . $Jcim->Plugin['ltd'] . '-3.7.css', array() , $Jcim->Ver );

			$translation = array( 'msg' => array( 'delete_confirm' => __( 'Confirm Deletion' ) ) );
			wp_localize_script( $Jcim->Plugin['page_slug'] , $Jcim->Plugin['ltd'] , $translation );

		}
		
	}

	function views() {

		global $Jcim;
		global $plugin_page;

		if( $this->is_settings_page() ) {
			
			$manage_page_path = $Jcim->Plugin['dir'] . trailingslashit( 'inc' );
			
			if( $plugin_page == $Jcim->Plugin['page_slug'] ) {
				
				if( !empty( $_GET ) && !empty( $_GET['tab'] ) && $_GET['tab'] == 'other' ) {
					
					include_once $manage_page_path . 'other.php';

				} else {
					
					include_once $manage_page_path . 'setting.php';
					
				}
				
			}
			
			add_filter( 'admin_footer_text' , array( $Jcim->ClassInfo , 'admin_footer_text' ) );
			
		}
		
	}
	
	function get_action_link() {
		
		global $Jcim;
		
		$url = remove_query_arg( array( $Jcim->Plugin['msg_notice'] , 'donated' ) );
		
		return $url;

	}
	
	function update_notice() {
		
		global $Jcim;

		if( $this->is_settings_page() ) {
			
			if( !empty( $_GET ) && !empty( $_GET[$Jcim->Plugin['msg_notice']] ) ) {
				
				$update_nag = $_GET[$Jcim->Plugin['msg_notice']];
				
				if( $update_nag == 'update' or $update_nag == 'delete' ) {

					printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Settings saved.' ) );

				}
				
			}
			
		}
		
	}
	
	function print_nav_tab_wrapper() {
		
		global $Jcim;
		
		$current = 'default';
		
		if( !empty( $_GET ) && !empty( $_GET['tab'] ) && $_GET['tab'] == 'other' )
			$current = 'other';
		
		$url_base = $Jcim->Plugin['links']['setting'];
		$tabs = array(
			'default' => array( 'url' => $url_base , 'label' => __( 'Include Manage Settings' , $Jcim->Plugin['ltd'] ) ),
			'other' => array( 'url' => add_query_arg( array( 'tab' => 'other' ) , $url_base ) , 'label' => __( 'Other Settings' , $Jcim->Plugin['ltd'] ) ),
		);
		
		echo '<h3 class="nav-tab-wrapper">';

		foreach( $tabs as $tab_name => $tab ) {
			$class = '';
			if( $current == $tab_name ) $class = 'nav-tab-active';
			printf( '<a href="%1$s" class="nav-tab %2$s">%3$s</a>' , $tab['url'] , $class , $tab['label'] );
		}
		
		echo '</h3>';
		
	}
	
	function jcim_get_load_header() {
		
		global $Jcim;

		check_ajax_referer( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] );
		
		if( empty( $_POST['data']['file_url'] ) )
			return false;
			
		$url = esc_url( $_POST['data']['file_url'] );
		
		$response = wp_remote_get( $url );
		$code = "";
		
		if ( is_wp_error( $response ) ) {
	
			$code = 'No Header';
	
	
		} elseif( 200 != wp_remote_retrieve_response_code( $response ) ) {
	
			$code = wp_remote_retrieve_response_code( $response ) . ' Error';
	
		}
		
		wp_send_json_success( array( 'code' => $code ) );
		
		die();
		
	}
	
}

endif;
