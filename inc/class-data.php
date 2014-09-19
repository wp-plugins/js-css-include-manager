<?php

if ( !class_exists( 'Jcim_Data' ) ) :

class Jcim_Data
{

	function __construct() {
		
		if( is_admin() )
			add_action( 'wp_loaded' , array( $this , 'init' ) , 20 );

	}

	function init() {
		
		global $Jcim;
		
		if( !$Jcim->Current['ajax'] ) {

			if( $Jcim->Current['multisite'] ) {

				add_action( 'admin_init' , array( $this , 'dataUpgrade' ) );
				
			}
			
			add_action( 'admin_init' , array( $this , 'dataUpdate' ) );

		}

	}

	private function get_record( $record ) {
		
		global $Jcim;
		
		$Data = array();

		if( $Jcim->Current['multisite'] ) {
			
			$GetData = get_site_option( $record );

		} else {

			$GetData = get_option( $record );

		}
		
		if( !empty( $GetData ) )
			$Data = $GetData;
		
		return $Data;

	}

	function get_data_include_manage() {
		
		global $Jcim;
		
		$Data = $this->get_record( $Jcim->Plugin['record']['setting'] );
		
		if( !empty( $Data ) ) {
			
			foreach( $Data as $key => $setting ) {
				
				if( !isset( $setting['data_ver'] ) )
					$Data[$key]['data_ver'] = 0;
				
			}
			
		}
		
		return $Data;

	}

	function get_data_others() {
		
		global $Jcim;
		
		$Data = $this->get_record( $Jcim->Plugin['record']['other'] );
		
		return $Data;

	}
	
	function get_current_data( $is_admin ) {

		global $Jcim;
		
		$GetData = $this->get_data_include_manage();
		$SettingsData = array();
		
		if( !empty( $GetData ) ) {
			
			foreach( $GetData as $key => $setting ) {
				
				if( empty( $setting['use'] ) or empty( $setting['filetype'] ) or empty( $setting['output'] ) or empty( $setting['condition'] ) or empty( $setting['location']['num'] ) or empty( $setting['location']['name'] ) ) {
					
					unset( $GetData[$key] );

				} else {
				
					if( !empty( $is_admin ) && $setting['use'] != '1' ) {
	
						unset( $GetData[$key] );
	
					} elseif( empty( $is_admin ) && $setting['use'] != '2' ) {
	
						unset( $GetData[$key] );
	
					}
					
				}
				
			}
			
			if( !empty( $GetData ) ) {

				$SettingsData = $GetData;
				
			}
			
		}

		return $SettingsData;

	}




	function dataUpgrade() {
		
		global $Jcim;
		
		if( empty( $_POST ) && !empty( $Jcim->Current['main_blog'] ) ) {
			
			$old_data = get_option( $Jcim->Plugin['record']['setting'] );

			if( !empty( $old_data ) ) {
				
				update_site_option( $Jcim->Plugin['record']['setting'] , $old_data );
				//delete_option( $Jcim->Plugin['record']['setting'] );

			}
			
		}
		
	}

	function dataUpdate() {
		
		global $Jcim;
		
		$RecordField = false;
		
		if( !empty( $_POST ) && !empty( $Jcim->ClassManager->is_manager ) && !empty( $_POST[$Jcim->Plugin['form']['field']] ) && $_POST[$Jcim->Plugin['form']['field']] == $Jcim->Plugin['UPFN']  ) {

			if( !empty( $_POST['record_field'] ) ) {
				
				$RecordField = strip_tags( $_POST['record_field'] );
				
				if( !empty( $_POST[$Jcim->Plugin['nonces']['field']] ) && check_admin_referer( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] ) ) {
						
					if( $RecordField == $Jcim->Plugin['record']['setting'] ) {
						
						if( !empty( $_POST['data']['delete'] ) ) {
		
							$this->update_delete();
		
						} elseif( !empty( $_POST['data']['add'] ) ) {
								
							$this->update_add();
		
						} elseif( !empty( $_POST['data']['update'] ) ) {
								
							$this->update_list();
		
						}
							
					} elseif( $RecordField == $Jcim->Plugin['record']['other'] ) {
								
						$this->update_other();
		
					}
						
				}
					
			}
				
		}

	}

	private function update_data_format( $list ) {
		
		global $Jcim;

		$setting = array();
		
		$setting['use'] = 1;
		if( !empty( $list['use'] ) )
			$setting['use'] = intval( $list['use'] );

		$setting['filetype'] = 1;
		if( !empty( $list['filetype'] ) )
			$setting['filetype'] = intval( $list['filetype'] );

		$setting['output'] = 1;
		if( !empty( $list['output'] ) )
			$setting['output'] = intval( $list['output'] );

		$setting['condition'] = 1;
		if( !empty( $list['condition'] ) )
			$setting['condition'] = intval( $list['condition'] );

		$setting['location']['num'] = 1;
		if( !empty( $list['location']['num'] ) )
			$setting['location']['num'] = intval( $list['location']['num'] );
		
		$setting['location']['name'] = '';
		if( !empty( $list['location']['name'] ) )
			$setting['location']['name'] = strip_tags( $list['location']['name'] );
		
		$setting['data_ver'] = 0;
		if( !empty( $list['data_ver'] ) )
			$setting['data_ver'] = intval( $list['data_ver'] );
		
		if( $Jcim->Current['multisite'] ) {

			$setting['standard'] = 'all';
			if( !empty( $list['standard'] ) )
				$setting['standard'] = strip_tags( $list['standard'] );
				
			$setting['subsites'] = array();
			if( !empty( $list['subsites'] ) ) {
				foreach( $list['subsites'] as $blog_id ) {
					$blog_id = intval( $blog_id );
					$setting['subsites'][$blog_id] = 1;
				}
			}

		}

		return $setting;

	}

	private function update_delete() {
		
		global $Jcim;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		$delete_ids = array();
		
		if( empty( $PostData['delete'] ) )
			return false;

		foreach( $PostData['delete'] as $id => $v ) {
			$delete_ids[] = intval( $id );
		}
		
		$Data = $this->get_data_include_manage();

		foreach( $delete_ids as $id ) {
			if( !empty( $Data[$id] ) )
				unset( $Data[$id] );
		}

		if( $Jcim->Current['multisite'] && $Jcim->Current['network_admin'] ) {
			
			update_site_option( $Jcim->Plugin['record']['setting'] , $Data );
			
		} else {

			update_option( $Jcim->Plugin['record']['setting'] , $Data );

		}

		wp_redirect( add_query_arg( $Jcim->Plugin['msg_notice'] , 'delete' ) . '#update' );
		exit;

	}
	
	private function update_add() {
		
		global $Jcim;

		if( empty( $_POST['data'] ) )
			return false;
		
		$PostData = $_POST['data'];
		
		if( empty( $PostData ) or empty( $PostData['add'] ) or empty( $PostData['add']['use'] ) or empty( $PostData['add']['filetype'] ) )
			return false;
		
		$Add_data = $this->update_data_format( $PostData['add'] );
		
		$Data = $this->get_data_include_manage();
		$Data[] = $Add_data;

		if( $Jcim->Current['multisite'] && $Jcim->Current['network_admin'] ) {
			
			update_site_option( $Jcim->Plugin['record']['setting'] , $Data );
			
		} else {

			update_option( $Jcim->Plugin['record']['setting'] , $Data );

		}

		wp_redirect( add_query_arg( $Jcim->Plugin['msg_notice'] , 'update' ) . '#update' );
		exit;

	}
	
	private function update_list() {
		
		global $Jcim;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		
		if( empty( $PostData ) or empty( $PostData['update'] ) )
			return false;

		$Data = array();

		foreach( $PostData['update'] as $key => $list ) {

			$Data[$key] = $this->update_data_format( $list );

		}
		
		if( $Jcim->Current['multisite'] && $Jcim->Current['network_admin'] ) {
			
			update_site_option( $Jcim->Plugin['record']['setting'] , $Data );
			
		} else {

			update_option( $Jcim->Plugin['record']['setting'] , $Data );

		}

		wp_redirect( add_query_arg( $Jcim->Plugin['msg_notice'] , 'update' ) . '#update' );
		exit;

	}
	
	private function update_other() {
		
		global $Jcim;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		
		if( empty( $PostData['other'] ) )
			return false;
		
		$OtherData = $PostData['other'];
		
		if( empty( $OtherData ) )
			return false;
			
		$Data = array();

		if( !empty( $OtherData['capability'] ) )
			$Data['capability'] = strip_tags( $OtherData['capability'] );
			
		if( $Jcim->Current['multisite'] && $Jcim->Current['network_admin'] ) {
			
			update_site_option( $Jcim->Plugin['record']['other'] , $Data );
			
		} else {

			update_option( $Jcim->Plugin['record']['other'] , $Data );

		}

		wp_redirect( add_query_arg( $Jcim->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
}

endif;
