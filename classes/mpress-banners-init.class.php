<?php

if ( ! class_exists( 'mPress_Banners_Init' ) ) {

	class mPress_Banners_Init {

		private static $instance = false;

		public static function get_instance() {
			$instance = self::$instance;
			if( ! $instance )
				$instance = new self();
			return $instance;
		}

		private $plugin;

		private function __construct() {
			$this->plugin = new mPress_Banners_Plugin(
				__( 'mPress Banners', 'mpress-banners' ),
				'mpress-banners',
				MPRESS_BANNERS_VERSION,
				dirname( dirname( __FILE__ ) ) . '/mpress-banners.php'
			);
			self::register_post_types();
			$this->add_meta_boxes();
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_shortcode( 'mpress_banner', array( 'mPress_Banners_Shortcode', 'shortcode' ) );
			if( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			}
		}

		function after_setup_theme() {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'mpress-banner-thumb', 300, 100, true );
		}

		public function wp_enqueue_scripts() {
			wp_register_style( $this->plugin->slug, $this->plugin->url . '/css/mpress-banners.css' );
			wp_register_script( $this->plugin->slug, $this->plugin->url . '/js/mpress-banners.js', array( 'jquery' ) );
		}

		public function admin_init() {
			add_filter( 'manage_mpress_banner_posts_columns', array( $this, 'manage_posts_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		}

		function manage_posts_columns( $cols ) {
			$new_cols = array();
			foreach ( $cols as $key => $val ) {
				$new_cols[$key] = $val;
				switch( $key ) {
					case 'cb':
						$new_cols['image'] = __( 'Banner Image', 'mpress-banners' );
						break;
					case 'title':
						$new_cols['id'] = __( 'Banner ID', 'mpress-banners' );
						$new_cols['link'] = __( 'Banner Link', 'mpress-banners' );
						break;
				}
			}
			return $new_cols;
		}

		function manage_posts_custom_column( $col, $post_id ) {
			switch ( $col ) {
				case 'id':
					echo $post_id;
					break;
				case 'image':
					if ( ! function_exists( 'has_post_thumbnail' ) || ! has_post_thumbnail( $post_id ) )
						_e( 'Featured image not set', 'mpress-banners' );
					else
						echo get_the_post_thumbnail( $post_id, 'mpress-banner-thumb' );
					break;
				case 'link':
					$link = get_post_meta( $post_id, '_mpress_banner_link', true );
					$link_action = get_post_meta( $post_id, '_mpress_banner_link_action', true );
					if( '_none' == $link_action ) {
						_e( 'Linking disabled', 'mpress-banners' );
					} else {
						echo $link ? $link : __( 'Link not provided', 'mpress-banners' );
					}
					break;
			}
		}

		public static function register_post_types() {
			new mPress_Banners_Post_Type(
				'mpress_banner',
				__( 'Banner', 'mpress-banners' ),
				__( 'Banners', 'mpress-banners' ),
				array(
					'file' => dirname( dirname( __FILE__ ) ) . '/mpress-banners.php',
					'slug' => 'banners',
					'args' => array(
						'public' => false,
						'show_ui' => true,
						'supports' => array( 'title', 'thumbnail' ),
					),
				)
			);
		}

		public function add_meta_boxes() {
			new mPress_Banners_Meta_Box(
				'mpress-banner-link',
				__( 'Banner Link', 'mpress-banners' ),
				dirname( dirname( __FILE__ ) ) . '/meta-boxes/banner-link.php',
				array(
					'post_type' => array( 'mpress_banner' ),
					'meta_keys' => array(
						'_mpress_banner_link',
						'_mpress_banner_link_action',
					),
					'nonce_name' => '_update_mpress_banner',
					'nonce_action' => __FILE__,
				)
			);
		}

	}

}