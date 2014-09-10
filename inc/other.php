<?php

global $Jcim;

$Data = $Jcim->ClassData->get_data_others();
$all_user_roles = $Jcim->ClassConfig->get_all_user_roles();
$capabilities = $all_user_roles['administrator']['capabilities'];
ksort( $capabilities );
?>

<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'Other Settings' , $Jcim->Plugin['ltd'] ); ?></h2>
	<?php $this->print_nav_tab_wrapper(); ?>

	<?php $class = $Jcim->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<?php include_once $Jcim->Plugin['dir'] . 'inc/information.php'; ?>
		
		</div>

		<div id="postbox-container-2" class="postbox-container">

			<form id="<?php echo $Jcim->Plugin['ltd']; ?>_other_form" class="<?php echo $Jcim->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Jcim->Plugin['form']['field']; ?>" value="Y">
				<?php wp_nonce_field( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Jcim->Plugin['record']['other']; ?>" />

				<h3><?php _e( 'Other Settings' , $Jcim->Plugin['ltd'] ); ?></h3>

				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="capability"><?php _e( 'Plugin' ); ?><?php _e( 'Settings' ); ?><?php _e( 'Capabilities' ); ?></label>
							</th>
							<td>
								<p><?php printf( __( 'Please choose the minimum role that can modify %s settings.' , $Jcim->Plugin['ltd'] ) , $Jcim->Plugin['name'] ); ?></p>
								<select name="data[other][capability]" id="capability">
									<?php $selected_cap = $this->get_manager_user_role(); ?>
									<?php if( !empty( $Data['capability'] ) ) $selected_cap = strip_tags( $Data['capability'] ); ?>
									<?php foreach( $capabilities as $capability => $v ): ?>
										<option value="<?php echo $capability; ?>" <?php selected( $selected_cap , $capability ); ?>><?php echo $capability; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save' ) ); ?>
	
			</form>
			
		</div>

		<div class="clear"></div>

	</div>

</div>
