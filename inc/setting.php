<?php

// get data
$Data = $this->get_data();
$donatedKey = get_option( $this->Record_d );
$nonce_v = wp_create_nonce( $this->Nonces["value"] );

// get settings
$Settings = $this->get_settings_conf();

// include js css
$ReadedJs = array( 'jquery' , 'jquery-ui-sortable' );
wp_enqueue_script( $this->PageSlug ,  $this->Url . $this->PluginSlug . '.js', $ReadedJs , $this->Ver );
wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '.css', array() , $this->Ver );

$translation_array = array( 'nonce' => $nonce_v , 'delete_cofirm' => __( 'Are you sure you want to delete?' , $this->ltd ) , 'ajax_url' => admin_url( 'admin-ajax.php' ) , 'UPFN' => 'Y' );
wp_localize_script( $this->PageSlug , $this->PageSlug , $translation_array );

$class = "";
if( get_option( $this->Record_dw ) ) $class .= ' full-width';
?>
<div class="wrap wrap_<?php echo $this->ltd; ?>">
	<div class="icon32" id="icon-options-general"></div>
	<?php echo $this->Msg; ?>
	<h2><?php echo $this->Name; ?></h2>

	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<div id="about_plugin">
	
				<?php if( $donatedKey == $this->DonateKey ) : ?>

					<div class="toggle-plugin"><span></span><a href="#"><?php echo esc_html__( 'Collapse' ); ?></a></div>
					<p class="description"><?php _e( 'Thank you for your donation.' , $this->ltd ); ?></p>

				<?php else: ?>

					<div class="stuffbox" id="donationbox">
						<div class="inside">
							<p style="color: #FFFFFF; font-size: 20px;"><?php _e( 'Donate' , $this->ltd ); ?></p>
							<p style="color: #FFFFFF;"><?php _e( 'You are contented with this plugin?<br />By the laws of Japan, Japan\'s new paypal user can not make a donation button.<br />So i would like you to buy this plugin as the replacement for the donation.' , $this->ltd ); ?></p>
							<p>&nbsp;</p>
							<p style="text-align: center;">
								<a href="<?php echo $this->AuthorUrl; ?>line-break-first-and-end/?utm_source=use_plugin&utm_medium=donate&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" class="button-primary" target="_blank">Line Break First and End</a>
							</p>
							<p>&nbsp;</p>
							<div class="donation_memo">
								<p><strong><?php _e( 'Features' , $this->ltd ); ?></strong></p>
								<p><?php _e( 'Line Break First and End plugin is In the visual editor TinyMCE, It is a plugin that will help when you will not be able to enter a line break.' , $this->ltd ); ?></p>
							</div>
							<div class="donation_memo">
								<p><strong><?php _e( 'The primary use of donations' , $this->ltd ); ?></strong></p>
								<ul>
									<li>- <?php _e( 'Liquidation of time and value' , $this->ltd ); ?></li>
									<li>- <?php _e( 'Additional suggestions feature' , $this->ltd ); ?></li>
									<li>- <?php _e( 'Maintain motivation' , $this->ltd ); ?></li>
									<li>- <?php _e( 'Ensure time as the father of Sunday' , $this->ltd ); ?></li>
								</ul>
							</div>
							<form id="donation_form" class="jcim_form" method="post" action="<?php echo remove_query_arg( $this->MsgQ ); ?>">
								<p style="color: #FFFFFF;"><?php _e( 'If you have already donated to.' , $this->ltd ); ?></p>
								<p style="color: #FFFFFF;"><?php _e( 'Please enter the \'Donation delete key\' that have been described in the \'Line Break First and End download page\'.' , $this->ltd ); ?></p>
								<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
								<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
								<input type="hidden" name="record_field" value="<?php echo $this->Record; ?>" />
								<p style="color: #FFFFFF;"><label for="donate_key"><?php _e( 'Donation delete key' , $this->ltd ); ?></label></p>
								<input type="text" name="donate_key" id="donate_key" value="" class="legular-text" />
								<input type="submit" class="button-primary" name="update" value="<?php _e( 'Submit' ); ?>" />
							</form>
						</div>
					</div>
		
			<?php endif; ?>
			
				<div class="stuffbox" id="aboutbox">
					<h3><span class="hndle"><?php _e( 'About plugin' , $this->ltd ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Version checked' , $this->ltd ); ?> : 3.6.1 - 3.8</p>
						<ul>
							<li><a href="http://wordpress.org/plugins/<?php echo $this->PluginSlug; ?>" target="_blank"><?php _e( 'Plugin\'s site' , $this->ltd ); ?></a></li>
							<li><a href="<?php echo $this->AuthorUrl; ?>?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $this->ltd ); ?></a></li>
							<li><a href="http://wordpress.org/support/plugin/<?php echo $this->PluginSlug; ?>" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
							<li><a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $this->PluginSlug; ?>" target="_blank"><?php _e( 'Reviews' , $this->ltd ); ?></a></li>
							<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
							<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
						</ul>
					</div>
				</div>
		
				<div class="stuffbox" id="usefulbox">
					<h3><span class="hndle"><?php _e( 'Useful plugins' , $this->ltd ); ?></span></h3>
					<div class="inside">
						<p><strong><a href="http://wordpress.org/extend/plugins/wp-admin-ui-customize/" target="_blank">WP Admin UI Customize</a></strong></p>
						<p class="description"><?php _e( 'Customize a variety of screen management.' , $this->ltd  ); ?></p>
						<p><strong><a href="http://wordpress.org/extend/plugins/custom-options-plus-post-in/" target="_blank">Custom Options Plus Post in</a></strong></p>
						<p class="description"><?php _e( 'The plugin that allows you to add the value of the options. Option value that you have created, can be used in addition to the template tag, Short code can be used in the body of the article.' , $this->ltd  ); ?></p>
						<p><strong><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank">Announce from the Dashboard</a></strong></p>
						<p class="description"><?php _e( 'Announce to display the dashboard. Change the display to a different user role.' , $this->ltd  ); ?></p>
						<p>&nbsp;</p>
					</div>
				</div>

			</div>

		</div>

		<div id="postbox-container-2" class="postbox-container">

			<?php $type = 'create'; ?>
			<div id="<?php echo $type; ?>">

				<h3><?php _e( 'Set a file to include:' , $this->ltd ); ?></h3>

				<form id="jcim_setting" class="jcim_form" method="post" action="<?php echo remove_query_arg( $this->MsgQ ); ?>">
					<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
					<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
					<input type="hidden" name="record_field" value="<?php echo $this->Record; ?>" />
					<input type="hidden" name="<?php echo $type; ?>[data_ver]" value="1" />
		
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $type; ?>_use"><?php _e( 'Panel Type' , $this->ltd ); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[use]" id="<?php echo $type; ?>_use">
										<?php foreach( $Settings["panel_type"] as $key => $val) : ?>
											<?php if( !empty( $val ) ) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_filetype"><?php _e( 'File Type' , $this->ltd ); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[filetype]" id="<?php echo $type; ?>_filetype">
										<?php foreach( $Settings["file_type"] as $key => $val) : ?>
											<?php if( !empty( $val ) ) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_output"><?php _e( 'Output' , $this->ltd ); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[output]" id="<?php echo $type; ?>_output">
										<?php foreach( $Settings["output"] as $key => $val) : ?>
											<?php if( !empty( $val ) ) : ?>
												<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_condition"><?php _e( 'Condition' , $this->ltd ); ?></label> *</th>
								<td>
									<select name="<?php echo $type; ?>[condition]" id="<?php echo $type; ?>condition">
										<?php foreach( $Settings["condition"] as $key => $cond) : ?>
											<?php if( !empty( $cond ) ) : ?>
												<option value="<?php echo $key; ?>">
													<?php if( empty( $cond["code"] ) ) : ?>
														<?php echo strip_tags( $cond["desc"] ); ?>
													<?php else: ?>
														<?php echo strip_tags( $cond["code"] ); ?>
													<?php endif; ?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
									<p><a href="#" class="condition_desc_show"><?php _e( 'Description of condition is here' , $this->ltd ); ?></a></p>
									<ul class="condition_desc">
										<?php foreach( $Settings["condition"] as $key => $cond) : ?>
											<?php if( !empty( $cond["code"] ) && !empty( $cond["help_link"] ) ) : ?>
												<li><code><a href="<?php echo esc_url( $cond["help_link"] ); ?>" target="_blank"><?php echo strip_tags( $cond["code"] ); ?></a></code> <span class="description"><?php echo strip_tags( $cond["desc"] ); ?></span></li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $type; ?>_location"><?php _e( 'Location' , $this->ltd ); ?></label> *</th>
								<td>
									<ul>
										<?php foreach($Settings["location"] as $key => $val) : ?>
											<?php if(!empty($val["name"])) : ?>
												<li>
													<label><input type="radio" class="location_radio" name="<?php echo $type; ?>[location][num]" value="<?php echo $key; ?>" /><?php echo _e($val["name"], $this->ltd ); ?></label>
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
						<input type="submit" class="button-primary" name="update" value="<?php _e( 'Save' ); ?>" />
					</p>
						
				</form>
					
			</div>

		</div>

		<div class="clear"></div>

	</div>

	<div class="metabox-holder columns-1">
	
		<h3><?php _e( 'Include setting that you created.' , $this->ltd  ); ?></h3>

		<div id="update">
		
			<?php if( !empty( $Data ) ) : ?>
			
				<table cellspacing="0" class="widefat fixed">
					<thead>
						<tr>
							<th class="use"><?php _e( 'Panel Type' , $this->ltd ); ?></th>
							<th class="filetype"><?php _e( 'File Type' , $this->ltd ); ?></th>
							<th class="output"><?php _e( 'Output', $this->ltd ); ?></th>
							<th class="condition"><?php _e( 'Condition' , $this->ltd ); ?></th>
							<th class="location"><?php _e( 'Location' , $this->ltd ); ?></th>
							<th class="operation">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php $altCount = 0; ?>
						<?php foreach($Data as $key => $val) : ?>

							<?php if( $altCount == 0 ): ?>
								<?php $altCount++; ?>
							<?php else: ?>
								<?php $altCount = 0; ?>
							<?php endif; ?>
							<?php echo $this->get_list( $key , $altCount , false ); ?>

						<?php endforeach; ?>

					</tbody>
				</table>

			<?php else: ?>
			
				<p><?php _e( 'Not created include setting.' , $this->ltd  ); ?></p>
			
			<?php endif; ?>
		</div>
	
	</div>

</div>
