/* jshint curly:false, forin:false, expr:true */
;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( el, options )
	{
		this.el = el;
		this.options = options;
		this.expanded = false;
		this.isAnimating = false;
		this.positions = ['top','left','bottom','right'];

		this.init();
	};

	Plugin.prototype =
	{
		defaults: {
			openEl: ".morph-open",
			closeEl: ".morph-close",
			contentEl: ".morph-content",
			onAfterClose: null,
			onAfterOpen: null,
			onBeforeClose: null,
			onBeforeOpen: null,
		},

		init: function()
		{
			this.config = castor._extend( {}, this.defaults, this.options, this.el.getAttribute( 'data-options' ));

			this.openEl = this.el.querySelector( this.config.openEl );
			this.closeEl = this.el.querySelector( this.config.closeEl );
			this.contentEl = this.el.querySelector( this.config.contentEl );

			this.transitionEndListener = this.transitionEnd.bind( this );

			this.el.classList.add( 'can-morph' );

			this.openEl.addEventListener( 'click', this.toggle.bind( this ));
			document.addEventListener( 'keyup', this.keypress.bind( this ));

			if( this.closeEl ) {
				this.closeEl.addEventListener( 'click', this.toggle.bind( this ));
			}

			return this;
		},

		callback: function( callback )
		{
			if( typeof this.config[ callback ] === 'function' ) {
				this.config[ callback ]( this.el );
			}
		},

		keypress: function( ev )
		{
			if( 27 === ( ev.keyCode || ev.which ) && this.expanded ) {
				this.toggle();
			}
		},

		toggle: function( ev )
		{
			if( ev !== undefined ) {
				ev.preventDefault();
			}

			if( this.isAnimating )return;

			this.isAnimating = true;

			this.contentEl.addEventListener( castor._getTransitionEvent(), this.transitionEndListener );

			this.el.classList.add( 'active' );

			this.expanded ? this.close() : this.open();
		},

		open: function()
		{
			document.body.classList.add( 'opened-morph' );

			this.callback( 'onBeforeOpen' );

			this.setPosition( this.getCoordinates());
		},

		close: function()
		{
			this.callback( 'onBeforeClose' );

			this.setPosition( this.getCoordinates());

			document.body.classList.remove( 'opened-morph' );
			this.el.classList.remove( 'open' );

			(new AnimationFrame()).request( function() {
				this.contentEl.classList.remove( 'no-transition' );
				this.setPosition( 0 );
			}.bind( this ));
		},

		transitionEnd: function( ev )
		{
			if( ev.target !== this.contentEl )return;

			if( !this.expanded && -1 === this.positions.indexOf( ev.propertyName ))return;

			this.isAnimating = false;

			this.contentEl.removeEventListener( castor._getTransitionEvent(), this.transitionEndListener );

			if( !this.expanded ) {
				this.contentEl.classList.add( 'no-transition' );

				(new AnimationFrame()).request( function() {
					this.el.classList.add( 'open' );
					this.el.classList.remove( 'active' );
				}.bind( this ));
			}
			else {
				this.el.classList.remove( 'active' );
			}

			this.callback( this.expanded ? 'onAfterClose' : 'onAfterOpen' );

			this.expanded = !this.expanded;
		},

		getCoordinates: function()
		{
			var coordinates = this.el.getClientRects()[0];

			return {
				top: -(coordinates.top) + 'px',
				left: -(coordinates.left) + 'px',
				bottom: -(window.innerHeight - coordinates.bottom) + 'px',
				right: -(window.innerWidth - coordinates.right) + 'px',
			};
		},

		setPosition: function( coordinates )
		{
			this.positions.forEach( function( position ) {
				this.contentEl.style[ position ] = ( coordinates === Object( coordinates )) ? coordinates[ position ] : coordinates;
			}.bind( this ));
		},
	};

	Plugin.defaults = Plugin.prototype.defaults;

	castor.MorphContent = Plugin;

})( window, document );
