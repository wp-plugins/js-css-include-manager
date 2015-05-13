<?php

if ( !class_exists( 'Jcim_Plugin_Info' ) ) :

class Jcim_Plugin_Info
{

	var $nonces = array();

	private $DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
	private $DonateRecord = '';
	private $DonateOptionRecord = '';

	function __construct() {
		
		add_action( 'wp_loaded' , array( $this , 'setup' ) , 20 );
		
	}

	function setup() {
		
		global $Jcim;
		
		$this->DonateRecord = $Jcim->Plugin['ltd'] . '_donated';
		$this->DonateOptionRecord = $Jcim->Plugin['ltd'] . '_donate_width';
		$this->nonces = array( 'field' => $Jcim->Plugin['nonces']['field'] . '_donate' , 'value' => $Jcim->Plugin['nonces']['value'] . '_donate' );
		
		if( $Jcim->Current['admin'] && $Jcim->ClassManager->is_manager ) {

			if( !$Jcim->Current['ajax'] ) {

				if( $Jcim->Current['multisite'] ) {

					add_action( 'network_admin_notices' , array( $this , 'donate_notice' ) );

				} else {

					add_action( 'admin_notices' , array( $this , 'donate_notice' ) );

				}

				add_action( 'admin_init' , array( $this , 'dataUpgrade' ) );
				add_action( 'admin_init' , array( $this , 'dataUpdate' ) );

			} else {

				add_action( 'wp_ajax_' . $Jcim->Plugin['ltd'] . '_donation_toggle' , array( $this , 'ajax_donation_toggle' ) );

			}

			add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );

		}

	}

	function dataUpgrade() {
		
		global $Jcim;
		
		if( empty( $_POST ) && !empty( $Jcim->Current['main_blog'] ) ) {
			
			$old_donated = get_option( $Jcim->Plugin['page_slug'] . '_donated' );

			if( !empty( $old_donated ) ) {
				
				if( !empty( $Jcim->Current['multisite'] ) ) {
							
					update_site_option( $this->DonateRecord , $old_donated );
							
				} else {
				
					update_option( $this->DonateRecord , $old_donated );
		
				}
				
				delete_option( $Jcim->Plugin['page_slug'] . '_donated' );

			}

			$old_donated_width = get_option( $Jcim->Plugin['page_slug'] . '_donated_width' );

			if( !empty( $old_donated_width ) ) {
				
				if( !empty( $Jcim->Current['multisite'] ) ) {
							
					update_site_option( $this->DonateOptionRecord , $old_donated_width );
							
				} else {
				
					update_option( $this->DonateOptionRecord , $old_donated_width );
		
				}
				
				delete_option( $Jcim->Plugin['page_slug'] . '_donated_width' );

			}

			
		}
		
	}

	function admin_print_scripts() {
		
		global $Jcim;
		
		if( $Jcim->ClassManager->is_settings_page() ) {
			
			$translation = array( $this->nonces['field'] => wp_create_nonce( $this->nonces['value'] ) );
			wp_localize_script( $Jcim->Plugin['page_slug'] , $Jcim->Plugin['ltd'] . '_donate' , $translation );

		}

	}

	function ajax_donation_toggle() {
		
		if( isset( $_POST['f'] ) ) {

			$is_donated = $this->is_donated();

			if( !empty( $is_donated ) ) {

				$this->update_donate_toggle( intval( $_POST['f'] ) );
			
			}

		}
		
		die();
		
	}

	function is_donated() {
		
		$donated = false;
		$donateKey = $this->get_donate_key( $this->DonateRecord );

		if( !empty( $donateKey ) && $donateKey == $this->DonateKey ) {
			$donated = true;
		}

		return $donated;

	}

	function donate_notice() {
		
		global $Jcim;
		
		$setting_page = $Jcim->ClassManager->is_settings_page();
		
		if( !empty( $setting_page ) ) {
		
			if( !empty( $_GET ) && !empty( $_GET[$Jcim->Plugin['msg_notice']] ) && $_GET[$Jcim->Plugin['msg_notice']] == 'donated' ) {

				printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Thank you for your donation.' , $Jcim->Plugin['ltd'] ) );

			} else {

				$is_donated = $this->is_donated();
	
				if( empty( $is_donated ) )
					printf( '<div class="updated"><p><strong><a href="%1$s" target="_blank">%2$s</a></strong></p></div>' , $this->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'footer' ) ) , __( 'Please consider making a donation.' , $Jcim->Plugin['ltd'] ) );
					
			}
				
		}

	}
	
	function version_checked() {

		global $Jcim;

		$readme = file_get_contents( $Jcim->Plugin['dir'] . 'readme.txt' );
		$items = explode( "\n" , $readme );
		$version_checked = '';
		foreach( $items as $key => $line ) {
			if( strpos( $line , 'Requires at least: ' ) !== false ) {
				$version_checked .= str_replace( 'Requires at least: ' , '' ,  $line );
				$version_checked .= ' - ';
			} elseif( strpos( $line , 'Tested up to: ' ) !== false ) {
				$version_checked .= str_replace( 'Tested up to: ' , '' ,  $line );
				break;
			}
		}
		
		return $version_checked;
		
	}

	function author_url( $args ) {
		
		$url = 'http://gqevu6bsiz.chicappa.jp/';
		
		if( !empty( $args['translate'] ) ) {
			$url .= 'please-translation/';
		} elseif( !empty( $args['donate'] ) ) {
			$url .= 'please-donation/';
		} elseif( !empty( $args['contact'] ) ) {
			$url .= 'contact-us/';
		}
		
		$url .= $this->get_utm_link( $args );

		return $url;

	}

	function get_utm_link( $args ) {
		
		global $Jcim;

		$url = '?utm_source=' . $args['tp'];
		$url .= '&utm_medium=' . $args['lc'];
		$url .= '&utm_content=' . $Jcim->Plugin['ltd'];
		$url .= '&utm_campaign=' . str_replace( '.' , '_' , $Jcim->Ver );

		return $url;

	}

	private function is_donate_key_check( $key ) {
		
		$check = false;
		$key = md5( strip_tags( $key ) );
		if( $this->DonateKey == $key )
			$check = $key;

		return $check;

	}

	function get_width_class() {
		
		global $Jcim;

		$class = $Jcim->Plugin['ltd'];
		
		if( $this->is_donated() ) {

			$width_option = $this->get_donate_width();

			if( !empty( $width_option ) )
				$class .= ' full-width';

		}
		
		return $class;

	}
	
	function get_gravatar_src( $size = 40 ) {
		
		global $Jcim;

		$img_src = $Jcim->Current['schema'] . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=' . $size;

		return $img_src;

	}

	function admin_footer_text() {
		
		$author_url = $this->author_url( array( 'tp' => 'use_plugin' , 'lc' => 'footer' ) );
		$text = sprintf( '<a href="%1$s" target="_blank"><img src="%2$s" width="18" /></a>' ,  $author_url , $this->get_gravatar_src( '18' ) );
		$text .= sprintf( 'Plugin developer : <a href="%s" target="_blank">gqevu6bsiz</a>' , $author_url );

		return $text;
		
	}

	private function get_donate_key( $record ) {
		
		global $Jcim;

		if( $Jcim->Current['multisite'] ) {

			$donateKey = get_site_option( $record );

		} else {

			$donateKey = get_option( $record );

		}
		
		return $donateKey;

	}

	private function get_donate_width() {
		
		global $Jcim;
		
		$width = false;
		if( $Jcim->Current['multisite'] ) {

			$GetData = get_site_option( $this->DonateOptionRecord );

		} else {

			$GetData = get_option( $this->DonateOptionRecord );

		}

		if( !empty( $GetData ) ) {
			$width = true;
		}

		return $width;

	}
	
	function dataUpdate() {
		
		global $Jcim;
		
		$RecordField = false;
		
		if( !empty( $_POST ) && !empty( $Jcim->ClassManager->is_manager ) && !empty( $_POST[$Jcim->Plugin['form']['field']] ) && $_POST[$Jcim->Plugin['form']['field']] == $Jcim->Plugin['UPFN'] ) {

			if( !empty( $_POST[$this->nonces['field']] ) && check_admin_referer( $this->nonces['value'] , $this->nonces['field'] ) ) {
					
				$this->update_donate();
					
			}

		}

	}
	
	private function update_donate() {
		
		global $Jcim;

		$is_donate_check = false;
		$submit_key = false;

		if( !empty( $_POST['donate_key'] ) ) {

			$is_donate_check = $this->is_donate_key_check( $_POST['donate_key'] );

			if( !empty( $is_donate_check ) ) {

				if( !empty( $Jcim->Current['multisite'] ) ) {
							
					update_site_option( $this->DonateRecord , $is_donate_check );
							
				} else {
				
					update_option( $this->DonateRecord , $is_donate_check );
		
				}

				wp_redirect( esc_url_raw( add_query_arg( $Jcim->Plugin['msg_notice'] , 'donated' ) ) );

			}

		}

	}

	private function update_donate_toggle( $Data ) {
		
		global $Jcim;

		if( $Jcim->ClassManager->is_manager && check_ajax_referer( $this->nonces['value'] , $this->nonces['field'] ) ) {

			if( $Jcim->Current['multisite'] ) {
						
				update_site_option( $this->DonateOptionRecord , $Data );
						
			} else {
			
				update_option( $this->DonateOptionRecord , $Data );

			}
			
		}

	}

}

endif;
