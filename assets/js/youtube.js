;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( videoEl, options )
	{
		this.hasLoaded = typeof YT === undefined;
		this.options = options;
		this.player = null;
		this.video = videoEl;
		this.init();
	};

	Plugin.prototype =
	{
		defaults: {
			iframe: '.video-embed',
			poster: '.video-poster',
			spinner: '.video-spinner',
		},

		init: function()
		{
			if( this.hasLoaded === false ) {
				this.injectScript();
			}
			this.config = castor._extend( {}, this.defaults, this.options );
			this.onYouTubePlayerAPIReady();
			castor._addClass( this.video.querySelector( this.config.poster ), 'hide' );
		},

		injectScript: function()
		{
			this.hasLoaded = true;
			var tag = document.createElement( 'script' );
			tag.src = "//www.youtube.com/player_api";
			var firstScriptTag = document.getElementsByTagName( 'script' )[0];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		},

		onReady: function()
		{
			var spinner = this.video.querySelector( this.config.spinner );
			this.player.playVideo();
			setTimeout( function() {
				castor._addClass( spinner, 'hide' );
			}, 1000 );
		},

		playerHasLoaded: function()
		{
			this.player = new YT.Player( this.video.querySelector( this.config.iframe ), {
				events: {
					onReady: this.onReady.bind( this ),
				},
			});
		},

		onYouTubePlayerAPIReady: function()
		{
			var self = this;
			var youtubeApiReadyExists = typeof window.castor.YouTubeAPIReady === undefined;
			setTimeout( function() {
				if( typeof window.onYouTubePlayerAPIReady !== undefined ) {
					if( !youtubeApiReadyExists ) {
						window.castor.YouTubeAPIReady = [];
					}
					window.castor.YouTubeAPIReady.push( window.onYouTubePlayerAPIReady );
				}
				window.onYouTubePlayerAPIReady = function() {
					self.playerHasLoaded();
					if( youtubeApiReadyExists ) {
						if( window.castor.YouTubeAPIReady.length ) {
							window.castor.YouTubeAPIReady.pop()();
						}
					}
				}
			}, 2 );
		},
	};

	castor.YouTube = Plugin;

})( window, document );
