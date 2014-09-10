<?php

global $Jcim;

$Data = $Jcim->ClassData->get_data_include_manage();

$screens = $Jcim->ClassConfig->get_screen_types();
$file_types = $Jcim->ClassConfig->get_file_types();
$outputs = $Jcim->ClassConfig->get_output_types();
$conditions = $Jcim->ClassConfig->get_conditions();
$locations = $Jcim->ClassConfig->get_locations();
?>
<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php echo $Jcim->Plugin['name']; ?></h2>
	<?php $this->print_nav_tab_wrapper(); ?>

	<?php $class = $Jcim->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<?php include_once $Jcim->Plugin['dir'] . 'inc/information.php'; ?>
		
		</div>

		<div id="postbox-container-2" class="postbox-container">

			<form id="<?php echo $Jcim->Plugin['ltd']; ?>_create_form" class="<?php echo $Jcim->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Jcim->Plugin['form']['field']; ?>" value="Y" />
				<?php wp_nonce_field( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Jcim->Plugin['record']['setting']; ?>" />
				<input type="hidden" name="data[add][data_ver]" value="1" />

				<?php $mode = 'add'; ?>
				
				<h3><?php _e( 'Set a file to include:' , $Jcim->Plugin['ltd'] ); ?></h3>

				<div id="<?php echo $mode; ?>">
				
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $mode; ?>_use"><?php _e( 'Screen Type' , $Jcim->Plugin['ltd'] ); ?></label> *</th>
								<td class="use"><?php $Jcim->fields_setting( $mode , 'use' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_filetype"><?php _e( 'File Type' , $Jcim->Plugin['ltd'] ); ?></label> *</th>
								<td class="filetype"><?php $Jcim->fields_setting( $mode , 'filetype' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_output"><?php _e( 'Output' , $Jcim->Plugin['ltd'] ); ?></label></th>
								<td class="output"><?php $Jcim->fields_setting( $mode , 'output' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_condition"><?php _e( 'Condition' , $Jcim->Plugin['ltd'] ); ?></label></th>
								<td class="condition"><?php $Jcim->fields_setting( $mode , 'condition' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_location"><?php _e( 'Location' , $Jcim->Plugin['ltd'] ); ?></label></th>
								<td class="location"><?php $Jcim->fields_setting( $mode , 'location' ); ?></td>
							</tr>
						</tbody>
					</table>

					<p class="spinner"></p>
					<?php submit_button( __( 'Save' ) ); ?>
		
				</div>
	
			</form>

		</div>

		<div class="clear"></div>

	</div>

	<div class="metabox-holder columns-1" id="jcim-lists">

		<div class="postbox-container">

			<?php if( empty( $Data ) ) : ?>
	
				<p><strong><?php _e( 'Not created include setting.' , $Jcim->Plugin['ltd'] ); ?></strong></p>
	
			<?php else : ?>

				<?php $mode = 'update'; ?>

				<div id="<?php echo $mode; ?>">

					<form id="<?php echo $Jcim->Plugin['ltd']; ?>_<?php echo $mode; ?>_form" class="<?php echo $Jcim->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">

						<input type="hidden" name="<?php echo $Jcim->Plugin['form']['field']; ?>" value="Y">
						<?php wp_nonce_field( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Jcim->Plugin['record']['setting']; ?>" />

						<h3><?php _e( 'Include setting that you created.' , $Jcim->Plugin['ltd'] ); ?></h3>

						<div class="tablenav top">
							<select name="action" class="action_sel">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
						<table cellspacing="0" class="widefat fixed">
							<?php $arr = array( 'thead' , 'tfoot' ); ?>
							<?php foreach( $arr as $tag ) : ?>
								<<?php echo $tag; ?>>
									<tr>
										<th class="check-column">
											<input type="checkbox" />
										</th>
										<th class="use">
											<?php _e( 'Screen Type' , $Jcim->Plugin['ltd'] ); ?>
										</th>
										<th class="filetype">
											<?php _e( 'File Type' , $Jcim->Plugin['ltd'] ); ?>
										</th>
										<th class="output">
											<?php _e( 'Output' , $Jcim->Plugin['ltd'] ); ?>
										</th>
										<th class="condition">
											<?php _e( 'Condition' , $Jcim->Plugin['ltd'] ); ?>
										</th>
										<th class="location">
											<?php _e( 'Location' , $Jcim->Plugin['ltd'] ); ?>
										</th>
										<th class="operation">&nbsp;</th>
									</tr>
								</<?php echo $tag; ?>>
							<?php endforeach; ?>
							<tbody>
								
								<?php $altClass = 'alternate'; ?>
								<?php foreach( $Data as $key => $include_manage ) : ?>
								
									<?php if( !empty( $altClass ) ): ?>
										<?php $altClass = ''; ?>
									<?php else: ?>
										<?php $altClass = 'alternate'; ?>
									<?php endif; ?>

									<?php $data_ver = intval( $include_manage['data_ver'] ); ?>

									<tr id="tr_<?php echo $key; ?>" class="<?php echo $Jcim->Plugin['ltd']; ?>_list_tr <?php echo $altClass; ?>">
										<th class="check-column">
											<input type="checkbox" name="data[update][<?php echo $key; ?>][id]" value="<?php echo $key; ?>" />
											<input type="hidden" name="data_ver" value="<?php echo $data_ver; ?>" />
										</th>
										<td class="use">
											<?php $use = intval( $include_manage['use'] ); ?>
											<div class="edit">
												<?php $Jcim->fields_setting( $mode , 'use' , $use , $key ); ?>
											</div>
											<div class="toggle use">
												<p><?php echo $screens[$use]; ?></p>
											</div>
										</td>
										<td class="filetype">
											<?php $filetype = intval( $include_manage['filetype'] ); ?>
											<div class="edit">
												<?php $Jcim->fields_setting( $mode , 'filetype' , $filetype , $key ); ?>
											</div>
											<div class="toggle filetype">
												<p><?php echo $file_types[$filetype]; ?></p>
											</div>
										</td>
										<td class="output">
											<?php $output = intval( $include_manage['output'] ); ?>
											<div class="edit">
												<?php $Jcim->fields_setting( $mode , 'output' , $output , $key ); ?>
											</div>
											<div class="toggle output">
												<p><?php echo $outputs[$output]; ?></p>
											</div>
										</td>
										<td class="condition">
											<?php $condition = intval( $include_manage['condition'] ); ?>
											<div class="edit">
												<?php $Jcim->fields_setting( $mode , 'condition' , $condition , $key ); ?>
											</div>
											<div class="toggle condition">
												<p>
													<?php if( !empty( $conditions[$condition]['code'] ) ) : ?>
														<code><?php echo $conditions[$condition]['code']; ?></code>
													<?php endif; ?>
													<?php if( !empty( $conditions[$condition]['desc'] ) ) : ?>
														<p class="description"><?php echo $conditions[$condition]['desc']; ?></p>
													<?php endif; ?>
												</p>
											</div>
										</td>
										<td class="location">
											<?php $location_num = intval( $include_manage['location']['num'] ); ?>
											<?php $location_name = strip_tags( $include_manage['location']['name'] ); ?>
											<div class="edit">
												<?php $Jcim->fields_setting( $mode , 'location' , array( 'num' => $location_num , 'name' => $location_name , 'ver' => $data_ver ) , $key ); ?>
											</div>
											<div class="toggle location">
												<?php $location = $Jcim->convert_location( $location_num , $data_ver , $locations[$location_num] ); ?>
												<?php $request_file = $location['location'] . $location_name; ?>
												<p>
													<a href="<?php echo esc_html( $request_file ); ?>" target="_blank"><?php echo esc_html( $request_file ); ?></a>
												</p>
												<code></code>
												<span class="spinner"></span>
											</div>
										</td>
										<td class="operation">
											<ul class="toggle menu">
												<li><a class="menu_edit" href="javascript:void(0);"><?php _e( 'Edit' ); ?></a> | </li>
												<li><a class="delete" href="<?php echo admin_url( 'options-general.php?page=' . $Jcim->Plugin['page_slug'] ); ?>" id="delete_<?php echo $key; ?>"><?php _e('Delete'); ?></a></li>
											</ul>
											<div class="edit">
												<span class="spinner"></span>
												<?php submit_button( __( 'Save' ) ); ?>
											</div>
										</td>
									</tr>
								
								<?php endforeach; ?>
							</tbody>
						</table>
						<div class="tablenav top">
							<select name="action2" class="action_sel">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
					</form>
				</div>

				<div id="<?php echo $Jcim->Plugin['ltd']; ?>_confirm">
					<div id="ConfirmSt">
						<p><?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ) , '' ); ?></p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e( 'Cancel' ); ?></a>
						<a class="button-secondary" id="deletebtn" href="javascript:void(0);" title=""><?php _e( 'Continue' ); ?></a>
					</div>
				</div>
				
				<div id="<?php echo $Jcim->Plugin['ltd']; ?>_delete">
					<form id="<?php echo $Jcim->Plugin['ltd']; ?>_delete_form" class="<?php echo $Jcim->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
						<input type="hidden" name="<?php echo $Jcim->Plugin['form']['field']; ?>" value="Y">
						<?php wp_nonce_field( $Jcim->Plugin['nonces']['value'] , $Jcim->Plugin['nonces']['field'] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Jcim->Plugin['record']['setting']; ?>" />
						<input type="hidden" name="action" value="delete" />
					</form>
				</div>

			<?php endif; ?>

		</div>

	</div>

</div>

<script>
jQuery(document).ready(function($) {
	
	$('#add input[type=submit]').on('click', function( ev ) {

		$(ev.target).parent().parent().find('.spinner').show();

	});

	$('#update table tbody td.operation .edit input[type=submit]').on('click', function( ev ) {

		$(ev.target).parent().parent().find('.spinner').show();

	});

	$(document).on('click', 'input.location_radio', function( ev ) {

		var $RadioList = $(this).parent().parent().parent();
		$RadioList.children('li').each( function( index , el ) {
			val = $(el).find('input.location_radio').val();
			if( val == $(ev.target).val() ) {
				$(el).find('input.location_name').removeClass('disabled');
				$(el).find('input.location_name').prop('disabled', false);
			} else {
				$(el).find('input.location_name').addClass('disabled');
				$(el).find('input.location_name').prop('disabled', true);
			}
		});

	});

	$(document).on('click', '.condition_desc_show', function( ev ) {

		$(ev.target).parent().parent().find('ul.condition_desc').slideToggle();
		return false;

	});

	$(document).on('click', '.condition_add_desc_show', function( ev ) {

		$(ev.target).parent().parent().find('.condition_add_desc').slideToggle();
		return false;

	});

	$(document).on('click', '#update table td.operation .menu a.menu_edit', function( ev ) {

		var TR = $(ev.target).parent().parent().parent().parent();
		TR.addClass('collapse');
		
		return false;

	});

	$(document).on('click', '#update table tbody tr td.operation .menu a.delete', function( ev ) {

		$('#jcim_delete_form').find('.delete_id').remove();
		var TR = $(ev.target).parent().parent().parent().parent();
		var URL = TR.find('td.location .toggle p a').text();
		var ID = $(ev.target).prop('id').replace('delete_', '');
		var delete_list = {}
		delete_list['tr_' + ID] = ID;
		
		delete_confirm_show( URL , delete_list );
		return false;

	});

	function delete_confirm_show( html , list ) {

		$('#jcim_delete_form .delete_id').remove();
		$('#jcim_confirm p strong').html( html );
		for(var key in list) {
			$('#jcim_delete_form').append('<input type="hidden" name="data[delete][' + list[key] + ']" class="delete_id" value="1" />');
		}
		tb_show( jcim.msg.delete_confirm , '#TB_inline?height=200&width=300&inlineId=jcim_confirm', '' );
		return false;

	}

	$(document).on('click', '#ConfirmSt a#cancelbtn', function( ev ) {

		$('#jcim_delete_form').find('.delete_id').remove();
		$(ev.target).parent().find('p strong').html('');
		$('#jcim_confirm').find('p strong').html('');

		tb_remove();
		return false;

	});

	$(document).on('click', '#ConfirmSt a#deletebtn', function( ev ) {

		$('#jcim_delete_form').submit();
		return false;

	});

	$(document).on('click', '#update .tablenav input.bulk', function( ev ) {
		
		var Action = $(ev.target).parent().find('select.action_sel option:selected').val();

		if( Action != "" ) {
			
			var del_check = false;
			var del_list = {};

			$(document).find('#update table tbody tr.jcim_list_tr').each( function( key , el ) {

				var TR = $(el);
				var $Checkbox = TR.find('th.check-column input[type=checkbox]');
				var checked = $Checkbox.prop('checked');
				if( checked ) {
					del_list[TR.prop('id')] = $Checkbox.val();
					del_check = true;
				}

			});
			
			if( del_check ) {
				var Html = '<ul>';
				for(var id in del_list) {
					Html += '<li>' + $(document).find('#update table tbody tr#' + id + ' td.location .toggle p a').text() + '</li>';
				}
				Html += '</ul>';

				delete_confirm_show( Html , del_list );
				return false;
			}

		}

	});
	
	$('#update table tbody tr').each( function( index , el ) {
		
		var URL = $(el).find('td.location .toggle p a').prop('href');
		$(el).find('td.location .toggle .spinner').show();

		var PostData = {
			action: 'jcim_get_load_header',
			<?php echo $Jcim->Plugin['nonces']['field']; ?>: '<?php echo wp_create_nonce( $Jcim->Plugin['nonces']['value'] ); ?>',
			data: {
				file_url: URL
			}
		};
		$.post( ajaxurl , PostData , function( response ) {
			
			if( typeof( response ) == 'object' && response.success ) {

				if( response.data.code ) {
					
					$(el).find('td.location .toggle code').html( response.data.code );
					
				} else {
					
					$(el).find('td.location .toggle code').hide();
					
				}

			}
			
			$(el).find('td.location .toggle .spinner').hide();
			return false;

		});
		
	});

});
</script>