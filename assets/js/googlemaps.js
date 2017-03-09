;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( selector, options, markerOptions )
	{
		this.el = castor._isString( selector ) ? document.querySelector( selector ) : selector;
		this.init( options, markerOptions );
	}

	Plugin.prototype =
	{
		defaults: {
			center: { lat:0, lng:0 },
			disableDoubleClickZoom: true,
			draggable: true,
			fullscreenControl: false,
			mapTypeControl: false,
			// mapTypeId: google.maps.MapTypeId.ROADMAP,
			overviewMapControl: false,
			rotateControl: false,
			scaleControl: false,
			scrollwheel: false,
			streetViewControl: false,
			styles: [],
			zoom: 16,
			zoomControl: true,
		},

		init: function( options, markerOptions )
		{
			this.options = castor._extend( {}, this.defaults, options, this.el.getAttribute( 'data-map-options' ));

			this.map = new google.maps.Map( this.el, this.options );

			this.markerOptions = castor._extend({
				position: this.options.center,
				animation: google.maps.Animation.DROP,
			}, markerOptions, this.el.getAttribute( 'data-marker-options' ), { map: this.map });

			this.marker = new google.maps.Marker( this.markerOptions );

			google.maps.event.addDomListener( window, 'resize', this.onResize.bind( this ));
		},

		onResize: function()
		{
			(new AnimationFrame()).request( function() {
				this.map.setCenter( this.marker.getPosition() );
			});
		},
	};

	Plugin.defaults = Plugin.prototype.defaults;

	castor.GoogleMaps = Plugin;

})( window, document );
