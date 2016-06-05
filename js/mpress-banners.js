(function( $ ){

    $.fn.mpressBanners = function() {

		var mpressBannerRotate = function( $banners, timeDelay, loop, $all_banners ) {
			if( $banners.length > 0 ) {
				var el = $banners.first();
				el.animate( { height: 'toggle' }, 1000, function() {
					setTimeout( function(){
						el.animate( { height: 'toggle' }, 1000, function() {
							$banners = $banners.slice(1);
							mpressBannerRotate( $banners, timeDelay, loop, $all_banners );
						} );
					}, timeDelay * 1000 );
				} );
			} else if( loop ) {
                mpressBannerRotate( $all_banners, timeDelay, loop, $all_banners );
            }
		};

		return this.each(function() {

			var $wrapper = $(this),
                $banners = $('.mpress-banner', $wrapper),
				timeDelay = $wrapper.attr('data-timedelay'),
                loop = $wrapper.hasClass('mpress-banner-loop');

			mpressBannerRotate( $banners, timeDelay, loop, $banners );

            $('.mpress-banner-dismiss').click(function(e) {
                e.preventDefault();
                $wrapper.hide();
            });

		} );

	};

	$(document).ready(function() {
		$('.mpress-banner-wrapper').not('.mpress-banner-persistent').mpressBanners();
	});

})( jQuery );