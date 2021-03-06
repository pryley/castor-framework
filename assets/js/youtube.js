;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( videoEl, options )
	{
		this.video = videoEl;
		if( this.video ) {
			this.hasLoaded = typeof YT === undefined;
			this.options = castor._extend( this.defaults, options );
			this.player = null;
			this.init();
		}
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
			this.onYouTubePlayerAPIReady();
			this.video.querySelector( this.options.poster ).classList.add( 'hide' );
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
			var spinner = this.video.querySelector( this.options.spinner );
			this.player.playVideo();
			setTimeout( function() {
				spinner.classList.add( 'hide' );
			}, 1000 );
		},

		playerHasLoaded: function()
		{
			this.player = new YT.Player( this.video.querySelector( this.options.iframe ), {
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
