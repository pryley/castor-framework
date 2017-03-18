;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( navigationSelector, options )
	{
		this.aF = new AnimationFrame();
		this.el = document.querySelector( navigationSelector );
		this.options = options;
		this.init();
	};

	Plugin.prototype =
	{
		defaults: {
			scrollDelta: 10,
			scrollOffset: 150,
		},

		init: function()
		{
			this.config = castor._extend( this.defaults, this.options );
			// castor._on( 'click', this.el.querySelector( '.open-main-menu' ), this.onClick.bind( this ));
			castor._on( 'scroll', window, this.onScroll.bind( this ));
		},

		autoHide: function()
		{
			var currentTop = window.pageYOffset;
			this.checkNavigation( currentTop );
			this.previousTop = currentTop;
			this.scrolling = false;
		},

		checkNavigation: function( currentTop )
		{
			// scrolling up
			if( this.previousTop - currentTop > this.config.scrollDelta ) {
				castor._removeClass( this.el, 'is-hidden' );
			}
			// scrolling down
			else if( currentTop - this.previousTop > this.config.scrollDelta && currentTop > this.config.scrollOffset ) {
				castor._addClass( this.el, 'is-hidden' );
			}
		},

		// onClick: function( ev )
		// {
		// 	ev.preventDefault();
		// 	castor._toggleClass( this.el, 'nav-open' );
		// },

		onScroll: function( ev )
		{
			if( this.el.clientHeight === 0 )return;
			if( !this.scrolling ) {
				this.scrolling = true;
				this.aF.request( this.autoHide.bind( this ));
			}
		},
	};

	castor.AutoHideNavigation = Plugin;

})( window, document );
