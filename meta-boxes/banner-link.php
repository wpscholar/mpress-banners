<?php

	global $post;

	$link = esc_attr( get_post_meta( $post->ID , '_mpress_banner_link', true ) );

	$link_action = esc_attr( get_post_meta( $post->ID, '_mpress_banner_link_action', true ) );
	$link_action = $link_action ? $link_action : '_self';

?>

<p>
	<label for="<?php echo 'mpress-banner-link-action'; ?>">
		<?php _e( 'Please choose a linking action for this banner:', 'mpress-banners' ); ?>
	</label><br />

	<select id="<?php echo 'mpress-banner-link-action'; ?>" name="_mpress_banner_link_action">

		<option value="_none"<?php selected( $link_action, '_none' ); ?>>
			<?php _e( 'Disable link', 'mpress-banners' ); ?>
		</option>

		<option value="_self"<?php selected( $link_action, '_self' ); ?>>
			<?php _e( 'Open link in same window', 'mpress-banners' ); ?>
		</option>

		<option value="_blank"<?php selected( $link_action, '_blank' ); ?>>
			<?php _e( 'Open link in new window', 'mpress-banners' ); ?>
		</option>

	</select>
</p>

<p>
	<label for="<?php echo 'mpress-banner-link'; ?>">
		<?php _e( 'Provide a URL that this banner will link to:', 'mpress-banners' ); ?>
	</label><br />

	<input type="url" id="<?php echo 'mpress-banner-link'; ?>" name="_mpress_banner_link"  value="<?php echo $link; ?>" placeholder="http://" size="50" />
</p>