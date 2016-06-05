<?php

if ( ! class_exists( 'mPress_Banners_Meta_Box' ) ) {

	class mPress_Banners_Meta_Box {

		private $callback_args = array(),
				$content_path,
				$context = 'normal',
				$has_invalid_entries = false,
				$id,
				$meta_keys = array(),
				$nonce_action = '-1',
				$nonce_name = '_wpnonce',
				$post_type = array( 'post' ),
				$priority = 'default',
				$title,
				$user_capability = 'edit_post';

		function __construct( $id, $title, $content_path, $args = array() ) {
			if( is_admin() ) {
				if( ! file_exists( $content_path ) ) {
					throw new Exception( sprintf( 'Meta box content path "%s" is invalid', $content_path ) );
				} else {
					$this->id = $id;
					$this->title = $title;
					$this->content_path = $content_path;
					$args = (object) $args;
					if( isset( $args->post_type ) )
						$this->post_type = (array) $args->post_type;
					if( isset( $args->context ) )
						$this->context = $this->validate_context( $args->context );
					if( isset( $args->priority ) )
						$this->priority = $this->validate_priority( $args->priority );
					if( isset( $args->callback_args ) )
						$this->callback_args = (array) $args->callback_args;
					if( isset( $args->meta_keys ) )
						$this->meta_keys = (array) $args->meta_keys;
					if( isset( $args->nonce_action ) )
						$this->nonce_action = $args->nonce_action;
					if( isset( $args->nonce_name ) )
						$this->nonce_name = $args->nonce_name;
					if( isset( $args->user_capability ) )
						$this->user_capability = $args->user_capability;
					add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
					add_action( 'save_post', array( $this, 'save_post_meta' ) );
					add_action( 'admin_notices', array( $this, 'display_error_message' ) );
				}
			}
		}

		function validate_context( $context ) {
			if( ! in_array( $context, array( 'normal', 'advanced', 'side' ) ) )
				$context = $this->context;
			return $context;
		}

		function validate_priority( $priority ) {
			if( ! in_array( $priority, array( 'default', 'high', 'low', 'core' ) ) )
				$priority = $this->priority;
			return $priority;
		}

		function add_meta_boxes(){
			foreach( $this->post_type as $post_type ){
				add_meta_box(
					$this->id,
					$this->title,
					array( $this, 'meta_box_content' ),
					$post_type,
					$this->context,
					$this->priority
				);
			}
		}

		function meta_box_content( $post ){
			extract( $this->callback_args );
			wp_nonce_field( $this->nonce_action, $this->nonce_name );
			include( $this->content_path );
			do_action( 'mpress_meta_box_content', $this->id, $post );
		}

		function save_post_meta( $post_id ){
			if( $this->is_time_to_save( $post_id ) ) {
				$this->filter_meta_keys();
				if( $meta = $this->get_meta_to_save() ) {
					foreach( $meta as $meta_key => $meta_value ){
						$current_meta_value = get_post_meta( $post_id, $meta_key, true );
						if( $meta_value === $current_meta_value )
							continue;
						$is_valid = apply_filters( 'mpress_meta_box-validate_meta', true, $this->id, $meta_key, $meta_value );
						if( $is_valid ) {
							if( empty( $meta_value ) )
								delete_post_meta( $post_id, $meta_key );
							else
								update_post_meta( $post_id, $meta_key, $meta_value );
						} else {
							$this->has_invalid_entries = true;
						}
					}
				}
				if( $this->has_invalid_entries )
					add_filter( 'redirect_post_location', array( $this, 'meta_box_error' ) );
			}
			return $post_id;
		}

		function is_time_to_save( $post_id ) {
			if( empty( $_POST ) )
				return false;
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return false;
			if ( ! in_array( $_POST['post_type'], $this->post_type ) )
				return false;
			if ( ! wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) )
				return false;
			if ( ! current_user_can( $this->user_capability, $post_id ) )
				return false;
			return true;
		}

		function filter_meta_keys() {
			$this->meta_keys = (array) apply_filters( 'mpress_meta_box-filter_meta_keys-' . $this->id, $this->meta_keys );
		}

		function get_meta_to_save() {
			$data = array();
			foreach( $this->meta_keys as $meta_key ) {
				if( isset( $_POST[$meta_key] ) ) {
					$data[$meta_key] = $_POST[$meta_key];
				}
			}
			return apply_filters( 'mpress_meta_box-filter_data-' . $this->id, $data);
		}

		function meta_box_error( $location ) {
			return add_query_arg( array( $this->id => 1 ), $location );
		}

		function display_error_message(){
			if( isset( $_GET[$this->id] ) && $_GET[$this->id] == 1 ) {
				printf(
					'<div class="error"><p>Invalid entry in the <b>%s</b> box. Please check your entries.</p></div>',
					$this->title
				);
			}
		}

	}

}