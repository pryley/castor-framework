;(function( window, document, castor ) {
	"use strict";

	var Plugin = function( navigationSelector, options )
	{
		this.el = document.querySelector( navigationSelector );
		if( this.el ) {
			this.options = castor._extend( this.defaults, options );
			this.init();
		}
	};

	Plugin.prototype = {

		defaults: {
			autohideSelector: null, // enables autohide
			interval: 60, // Math.floor(1000/16.7)
			isHiddenClass: 'menu-is-hidden',
			isOpenClass: 'menu-is-open',
			minWidth: 768,
			onResizeEvent: null,
			onScrollEvent: null,
			onToggleEvent: null,
			scrollDelta: 10,
			scrollOffset: 150,
			toggleSelector: '.menu-toggle',
		},

		/** @return void */
		autoHide: function() {
			var currentTop = window.pageYOffset;
			this.checkNavigation( currentTop );
			this.previousTop = currentTop;
			this.isScrolling = false;
		},

		/** @return void */
		checkNavigation: function( currentTop ) {
			// scrolling up
			if( this.previousTop - currentTop > this.options.scrollDelta ) {
				this.bodyEl.classList.remove( this.options.isHiddenClass );
			}
			// scrolling down
			else if( currentTop - this.previousTop > this.options.scrollDelta && currentTop > this.options.scrollOffset ) {
				this.bodyEl.classList.add( this.options.isHiddenClass );
			}
		},

		/** @return void */
		fixScrollStutterOnIos: function() {
			if( typeof window.orientation === 'undefined' )return;
			this.autohideEl.style.height = '';
			this.autohideEl.style.height = this.autohideEl.clientHeight + 'px';
		},

		/** @return void */
		init: function() {
			this.aF = new AnimationFrame( this.options.interval );
			this.autohideEl = document.querySelector( this.options.autohideSelector );
			this.bodyEl = document.querySelector( 'body' );
			this.isAnimating = false;
			this.isScrolling = false;
			this.toggleEls = document.querySelectorAll( this.options.toggleSelector );
			this.transitionEnd = castor._cssEventEnd( 'transition' );
			this.initEvents();
		},

		/** @return void */
		initEvents: function() {
			[].forEach.call( this.toggleEls, function( el ) {
				el.addEventListener( 'click', this.onToggle.bind( this ));
				el.addEventListener( 'touchend', this.onToggle.bind( this ));
			}.bind( this ));
			window.addEventListener( 'resize', this.onResize.bind( this ));
			if( this.autoHideEl ) {
				this.fixScrollStutterOnIos();
				window.addEventListener( 'scroll', this.onScroll.bind( this ));
				window.addEventListener( 'orientationchange', this.fixScrollStutterOnIos.bind( this ));
			}
		},

		/** @return void */
		onResize: function( ev ) {
			this.aF.request( function() {
				if( window.outerWidth < this.options.minWidth || !this.bodyEl.classList.contains( this.options.isOpenClass ))return;
				this.bodyEl.classList.remove( this.options.isOpenClass );
				this.trigger( 'onResizeEvent' );
			}.bind( this ));
		},

		/** @return void */
		onScroll: function( ev ) {
			this.aF.request( function() {
				if( window.outerWidth < this.options.minWidth || this.el.clientHeight === 0 )return;
				if( !this.isScrolling ) {
					this.isScrolling = true;
					this.autoHide();
					this.trigger( 'onScrollEvent' );
				}
			}.bind( this ));
		},

		/** @return void */
		onToggle: function( ev ) {
			ev.preventDefault();
			if( this.transitionEnd ) {
				this.isAnimating = true;
			}
			this.bodyEl.classList.toggle( this.options.isOpenClass );
			this.trigger( 'onToggleEvent' );
			if( this.isAnimating )return;
			this.el.addEventListener( this.transitionEnd, this.onToggleEnd.bind( this ));
		},

		/** @return void */
		onToggleEnd: function() {
			this.isAnimating = false;
			this.el.removeEventListener( this.transitionEnd, this.onToggleEnd.bind( this ));
		},

		/** @return void */
		trigger: function( callback ) {
			if( typeof this.options[callback] === 'function' ) {
				this.options[callback].call( this );
			}
		},
	};

	castor.ResponsiveNavigation = Plugin;

})( window, document, window.castor );
