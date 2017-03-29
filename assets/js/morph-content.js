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

			castor._addClass( this.el, 'can-morph' );

			castor._on( "click", this.openEl, this.toggle.bind( this ));
			castor._on( "keyup", document, this.keypress.bind( this ));

			if( this.closeEl ) {
				castor._on( "click", this.closeEl, this.toggle.bind( this ));
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

			castor._on( castor._getTransitionEvent(), this.contentEl, this.transitionEndListener );

			castor._addClass( this.el, 'active' );

			this.expanded ? this.close() : this.open();
		},

		open: function()
		{
			castor._addClass( document.body, 'opened-morph' );

			this.callback( 'onBeforeOpen' );

			this.setPosition( this.getCoordinates());
		},

		close: function()
		{
			this.callback( 'onBeforeClose' );

			this.setPosition( this.getCoordinates());

			castor._removeClass( document.body, 'opened-morph' );
			castor._removeClass( this.el, 'open' );

			(new AnimationFrame()).request( function() {
				castor._removeClass( this.contentEl, 'no-transition' );
				this.setPosition( 0 );
			}.bind( this ));
		},

		transitionEnd: function( ev )
		{
			if( ev.target !== this.contentEl )return;

			if( !this.expanded && -1 === this.positions.indexOf( ev.propertyName ))return;

			this.isAnimating = false;

			castor._off( castor._getTransitionEvent(), this.contentEl, this.transitionEndListener );

			if( !this.expanded ) {
				castor._addClass( this.contentEl, 'no-transition' );

				(new AnimationFrame()).request( function() {
					castor._addClass( this.el, 'open' );
					castor._removeClass( this.el, 'active' );
				}.bind( this ));
			}
			else {
				castor._removeClass( this.el, 'active' );
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
