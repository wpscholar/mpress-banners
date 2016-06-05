<?php

if ( ! class_exists( 'mPress_Banners_Post_Type' ) ) {

	class mPress_Banners_Post_Type {

		private $post_type,
				$singular,
				$plural,
				$slug,
				$args;

		function __construct( $post_type, $singular, $plural, $args = array() ) {
			if( ! is_string( $post_type ) ) {
				throw new Exception( 'Post type name must be a string' );
			} else if( ! is_string( $singular ) ) {
				throw new Exception( 'Singular post type label must be a string' );
			} else if( ! is_string( $plural ) ) {
				throw new Exception( 'Plural post type label must be a string' );
			} else if( strlen( $post_type ) > 20 ){
				throw new Exception( sprintf( 'The post type name "%s" is over 20 characters in length', $post_type ) );
			} else {
				$this->post_type = $post_type;
				$this->singular = $singular;
				$this->plural = $plural;
				$slug = isset( $args['slug'] ) ? $args['slug']: $this->post_type;
				$this->slug = $this->sanitize_slug( $slug );

				/**
				 * NOTICE: This will only work when called prior to 'plugins_loaded' hook!
				 */
				if( isset( $args['file'] ) ){
					register_activation_hook( $args['file'], array( $this, 'activation' ) );
					register_deactivation_hook( $args['file'], array( $this, 'deactivation' ) );
				}

				$args = isset( $args['args'] ) && is_array( $args['args'] ) ? $args['args']: array();
				$this->set_post_type_args( $args );
				$this->version_check();

				add_action( 'init', array( $this, 'register_post_type' ) );
				add_filter( 'body_class', array( $this, 'body_class' ) );
			}
		}

		function activation(){
			$this->register_post_type();
			flush_rewrite_rules();
		}

		function deactivation(){
			flush_rewrite_rules();
		}

		function sanitize_slug( $slug ) {
			return preg_replace('/\s/', '-', strtolower( $slug ) );
		}

		function set_post_type_args( $args ) {
			$defaults = array(
				'labels' => array(
					'name' => $this->plural,
					'singular_name' => $this->singular,
					'add_new' => __( 'Add New ' ) . $this->singular,
					'add_new_item' => __( 'Add New ' ) . $this->singular,
					'edit_item' => __( 'Edit ' ) . $this->singular,
					'new_item' => __( 'New ' ) . $this->singular,
					'view_item' => __( 'View ' ) . $this->singular,
					'search_items' => __( 'Search ' ) . $this->plural,
					'not_found' => sprintf( __( 'No %s found' ), $this->plural ),
					'not_found_in_trash' => sprintf( __( 'No %s found in Trash' ), $this->plural )
				),
				'has_archive' => true,
				'public' => true,
				'rewrite' => array( 'slug' => $this->slug ),
			);
			$args = wp_parse_args( $args, $defaults );
			$this->args = apply_filters( 'mpress_post_type-register_' . $this->post_type, $args );
		}

		function register_post_type() {
			register_post_type( $this->post_type, $this->args );
		}

		function version_check() {
			global $wp_version;
			// Fail in really old versions of WordPress
			if( version_compare( $wp_version, '2.9', '<' ) ){
				throw new Exception( 'WordPress custom post types are not supported prior to version 2.9' );
			}
			if( version_compare( $wp_version, '3.1', '<' ) ){
				// Fix for template loading in older versions of WordPress
				add_filter( 'template_include', array( $this, 'fix_template_loading' ) );
				// Add rewrite rules for older versions of WordPress
				if( $this->args['rewrite'] ) {
					add_filter( 'generate_rewrite_rules', array( $this, 'generate_rewrite_rules' ) );
				}
			}
			// Add canonical link to post type archive page in WordPress 3.1+
			if( function_exists( 'get_post_type_archive_link' ) ) {
				add_action( 'wp_head', array( $this, 'add_canonical_link' ) );
			}
		}

		function fix_template_loading( $template ) {
			if ( get_query_var( 'post_type' ) == $this->post_type ) {
				if( is_singular() ) {
					$new_template = locate_template( "single-{$this->post_type}.php" );
				} else {
					$new_template = locate_template( "archive-{$this->post_type}.php" );
				}
				if( $new_template ){
					$template = $new_template;
				}
			}
			return $template;
		}

		function body_class( $classes ){
			if ( get_query_var('post_type') === $this->post_type ) {
				$classes[] = 'type-' . $this->post_type;
			}
			return $classes;
		}

		/*
		 * Handles rewrite rules for our custom post type if we are not using WordPres 3.1 or greater
		 */
		function generate_rewrite_rules( $wp_rewrite ) {
			$new_rules = array();
			// Does our post type support archives?
			$has_archive = isset( $this->args['has_archive'] ) && $this->args['has_archive'] ? true : false;
			// Does our post type support feeds?
			if( isset( $this->args['rewrite']['feeds'] ) ) {
				$feeds = $this->args['rewrite']['feeds'] ? true: false;
			} else {
				$feeds = $has_archive;
			}
			// Does our post type support pagination?
			if( isset( $this->args['rewrite']['pages'] ) ) {
				$pages = $this->args['rewrite']['pages'] ? true : false;
			} else {
				$pages = true;
			}
			// This rewrite rule is not necessary in WP 3.1 because of the has_archive argument
			if( $has_archive ) {
				$new_rules[$this->slug . '/?$'] = 'index.php?post_type=' . $this->post_type;
			}
			// This rewrite rule is not necessary in WP 3.1 because of the rewrite['feeds'] argument
			if( $feeds ) {
				$new_rules[$this->slug . '/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type=' . $this->post_type . '&feed=' . $wp_rewrite->preg_index(1);
			}
			// This rewrite rule is not necessary in WP 3.1 due to the rewrite['pages'] argument
			if( $pages ) {
				$new_rules[$this->slug . '/page/?([0-9]{1,})/?$'] = 'index.php?post_type=' . $this->post_type . '&paged=' . $wp_rewrite->preg_index(1);
			}
			$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );
			return $wp_rewrite;
		}

		/**
		 * Outputs the canonical link for the post type archive page in WordPress 3.1 and up
		 * This should be done for older versions of WordPress, but the logic for getting the post
		 * type archive link is a bit involved.
		 */
		function add_canonical_link(){
			if( get_query_var('post_type') == $this->post_type && !is_single() ){
				echo '<link rel="canonical" href="'. get_post_type_archive_link( $this->post_type ) .'"/>';
			}
		}

	}

}