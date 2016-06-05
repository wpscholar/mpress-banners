<?php

if ( ! class_exists( 'mPress_Banners_Shortcode' ) ) {

	class mPress_Banners_Shortcode {

		private static $atts = array(
            'id' => false,
			'banner' => array(),
			'time_delay' => 5,
			'direction' => 'down',
            'persistent' => false,
            'loop' => false,
            'dismissible' => true,
            'random' => false,
		);

		public static function shortcode( $atts, $content = false ) {
			$attributes = (object) self::validate_attributes( shortcode_atts( self::$atts, $atts ) );
			$time_delay = esc_attr( $attributes->time_delay );
			$class = 'mpress-banner mpress-banner-' . $attributes->direction;
            wp_enqueue_script( 'mpress-banners' );
			wp_enqueue_style( 'mpress-banners' );
			if( ! $content ) {
				if( empty( $attributes->banner ) ) {
					if( current_user_can('administrator') ) {
						$msg = <<<MSG
<div class="mpress-error" style="background:#FAC5C5; border:1px solid red; padding: 10px;">
	<strong>mPress Banners:</strong>
	<p>
		We don't know what banner you want to show here.  Did you mean to do something like this?<br />
		<code>[mpress_banner banner="1" time_delay="10"]</code><br />
		<code>[mpress_banner banner="1, 12, 310" direction="up"]</code><br />
		<code>[mpress_banner]&lt;a href="{link_url}"&gt;&lt;img src="{image_url}" /&gt;&lt;/a&gt;[/mpress_banner]</code>
	</p>
</div>
MSG;
						return $msg;
					}
					return false;
				}
				$return = '';
				if( $posts = self::fetch_posts( $attributes->banner, $attributes->random ) ) {
                    $classes = array( 'mpress-banner-wrapper' );
                    $id = $attributes->id ? ' id="' . esc_attr( $attributes->id ) . '"' : '';
                    if( $attributes->persistent ) {
                        $classes[] = 'mpress-banner-persistent';
                    }
                    if( $attributes->loop ) {
                        $classes[] = 'mpress-banner-loop';
                    }
                    if( $attributes->dismissible ) {
                        $classes[] = 'mpress-banner-dismissible';
                    }
					$return .= '<div'. $id .' class="'. join(' ', $classes) .'" data-timedelay="'.$time_delay.'">';
					foreach( $posts as $post ) {
						$image = get_the_post_thumbnail( $post->ID, 'full' );
						$link = get_post_meta( $post->ID, '_mpress_banner_link', true );
						$link_action = get_post_meta( $post->ID, '_mpress_banner_link_action', true );
						$link_action = $link_action ? $link_action : '_self';
						if( ! $link || '_none' == $link_action ) {
							$banner = $image;
						} else {
							$href = esc_url_raw( $link );
							$target = esc_attr( $link_action );
							$banner = "<a href=\"{$href}\" target=\"{$target}\">{$image}</a>";
						}
						$return .= <<<HTML
<div class="{$class}"><a href="#" class="mpress-banner-dismiss">X</a>
	{$banner}
</div>
HTML;
					}
					$return .= '</div>';
					return $return;
				} else {
					if( current_user_can('administrator') ) {
						$msg = <<<MSG
<div class="mpress-error" style="background:#FAC5C5; border:1px solid red; padding: 10px;">
	<strong>mPress Banners:</strong>
	<p>
		Sorry, it appears as though the banner(s) you assigned to this shortcode may not be published.
	</p>
</div>
MSG;
						return $msg;
					}
				}

			} else {
				$content = esc_html( $content );
				return <<<CONTENT
<div class="mpress-banner-wrapper" data-timedelay="{$time_delay}">
	<div class="{$class}">
		{$content}
	</div>
</div>
CONTENT;
			}
            return false;
		}

		private static function fetch_posts( $banners, $random = false ) {
			$posts = array();
			if( $banners && is_array( $banners ) ) {
                if( $random ) {
                    shuffle( $banners );
                }
				foreach( $banners as $banner_id ) {
					if( ( $post = get_post( $banner_id ) ) && 'publish' == $post->post_status ) {
						$posts[] = $post;
					}
				}
			}
			return $posts;
		}

		private static function validate_attributes( $atts ) {
			if( isset( $atts['banner'] ) && ! is_array( $atts['banner'] ) )
				$atts['banner'] = explode( ',', preg_replace('#[^0-9,]#', '', $atts['banner']) );
			if( isset( $atts['time_delay'] ) )
				$atts['time_delay'] = absint( $atts['time_delay'] ) ? absint( $atts['time_delay'] ) : self::$atts['time_delay'];
			if( isset( $atts['direction'] ) )
				$atts['direction'] = 'up' == strtolower( $atts['direction'] ) ? 'up': self::$atts['direction'];
            if( isset( $atts['persistent'] ) )
                $atts['persistent'] = 'true' == strtolower( $atts['persistent'] ) ? true: self::$atts['persistent'];
            if( isset( $atts['loop'] ) )
                $atts['loop'] = 'true' == strtolower( $atts['loop'] ) ? true: self::$atts['loop'];
            if( isset( $atts['id'] ) )
                $atts['id'] = preg_replace( '#[^a-z0-9-_]#i', '', $atts['id'] );
            if( isset( $atts['dismissible'] ) )
                $atts['dissmissable'] = 'false' == strtolower( $atts['dismissible'] ) ? false: self::$atts['dismissible'];
            if( isset( $atts['random'] ) )
                $atts['random'] = 'true' == strtolower( $atts['random'] ) ? true: self::$atts['random'];
			return $atts;
		}

	}

}