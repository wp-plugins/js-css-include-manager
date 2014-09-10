<?php if( $Jcim->ClassInfo->is_donated() ) : ?>

	<p class="donated_message description"><?php _e( 'Thank you for your donation.' , $Jcim->Plugin['ltd'] ); ?></p>
	<div class="toggle-width">
		<a href="javascript:void(0);" class="collapse-sidebar button-secondary">
			<span class="collapse-sidebar-arrow"></span>
			<span class="collapse-sidebar-label"><?php echo esc_html__( 'Collapse' ); ?></span>
		</a>
	</div>

<?php else: ?>

	<div class="stuffbox" id="donationbox">
		<h3><span class="hndle"><?php _e( 'Please consider making a donation.' , $Jcim->Plugin['ltd'] ); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Thank you very much for your support.' , $Jcim->Plugin['ltd'] ); ?></p>
			<p><a href="<?php echo $Jcim->ClassInfo->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'donate' ) ); ?>" class="button button-primary" target="_blank"><?php _e( 'Donate' , $Jcim->Plugin['ltd'] ); ?></a></p>
			<p><?php _e( 'Please enter the \'Donation delete key\' that have been described in the \'Line Break First and End download page\'.' , $Jcim->Plugin['ltd'] ); ?></p>
			<form id="<?php echo $Jcim->Plugin['ltd']; ?>_donation_form" class="<?php echo $Jcim->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Jcim->Plugin['ltd']; ?>_settings" value="Y">
				<?php wp_nonce_field( $Jcim->ClassInfo->nonces['value'] , $Jcim->ClassInfo->nonces['field'] ); ?>
				<label for="donate_key"><?php _e( 'Donation delete key' , $Jcim->Plugin['ltd'] ); ?></label>
				<input type="text" name="donate_key" id="donate_key" value="" class="large-text" />
				<?php submit_button( __( 'Submit' ) , 'secondary' ); ?>
			</form>
		</div>
	</div>

<?php endif; ?>

<div class="stuffbox" id="considerbox">
	<h3><span class="hndle"><?php _e( 'Have you want to customize?' , $Jcim->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p style="float: right;">
			<a href="<?php echo $Jcim->ClassInfo->author_url( array( 'contact' => 1 , 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank">
				<img src="<?php echo $Jcim->ClassInfo->get_gravatar_src( '46' ); ?>" width="46" />
			</a>
		</p>
		<p><?php _e( 'I am good at Admin Screen Customize.' , $Jcim->Plugin['ltd'] ); ?></p>
		<p><?php _e( 'Please consider the request to me if it is good.' , $Jcim->Plugin['ltd'] ); ?></p>
		<p>
			<a href="<?php echo $Jcim->ClassInfo->author_url( array( 'contact' => 1 , 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Contact' , $Jcim->Plugin['ltd'] ); ?></a>
			| 
			<a href="http://wpadminuicustomize.com/blog/category/example/<?php echo $Jcim->ClassInfo->get_utm_link( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Example Customize' , $Jcim->Plugin['ltd'] ); ?></a>
	</div>
</div>

<div class="stuffbox" id="aboutbox">
	<h3><span class="hndle"><?php _e( 'About plugin' , $Jcim->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p><?php _e( 'Version checked' , $Jcim->Plugin['ltd'] ); ?> : <?php echo $Jcim->ClassInfo->version_checked(); ?></p>
		<ul>
			<li><a href="<?php echo $Jcim->ClassInfo->author_url( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $Jcim->Plugin['ltd'] ); ?></a></li>
			<li><a href="<?php echo $Jcim->Plugin['links']['forum']; ?>" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
			<li><a href="<?php echo $Jcim->Plugin['links']['review']; ?>" target="_blank"><?php _e( 'Reviews' , $Jcim->Plugin['ltd'] ); ?></a></li>
			<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
			<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
		</ul>
	</div>
</div>

<div class="stuffbox" id="usefulbox">
	<h3><span class="hndle"><?php _e( 'Useful plugins' , $Jcim->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p><strong><a href="http://wpadminuicustomize.com/<?php echo $Jcim->ClassInfo->get_utm_link( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
		<p class="description"><?php _e( 'Customize a variety of screen management.' , $Jcim->Plugin['ltd'] ); ?></p>
		<p><strong><a href="http://wordpress.org/extend/plugins/post-lists-view-custom/" target="_blank">Post Lists View Custom</a></strong></p>
		<p class="description"><?php _e( 'Customize the list of the post and page. custom post type page, too. You can customize the column display items freely.' , $Jcim->Plugin['ltd'] ); ?></p>
		<p><strong><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank">Announce from the Dashboard</a></strong></p>
		<p class="description"><?php _e( 'Announce to display the dashboard. Change the display to a different user role.' , $Jcim->Plugin['ltd']  ); ?></p>
		<p>&nbsp;</p>
		<p><a href="<?php echo $Jcim->Plugin['links']['profile']; ?>" target="_blank"><?php _e( 'Plugins' ); ?></a></p>
	</div>
</div>
