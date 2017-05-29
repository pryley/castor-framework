/*jshint curly:false,forin:false*/

AnimationFrame.shim();

window.castor =
{
	_hasClass: function( el, className ) {
		if( el.classList ) return el.classList.contains( className );
		else return new RegExp( '\\b' + className + '\\b' ).test( el.className );
	},
	_addClass: function( el, className ) {
		className.split( ' ' ).forEach( function( str ) {
			if( el.classList ) el.classList.add( str );
			else if( !this._hasClass( el, str )) el.className += ' ' + str;
		});
		return el;
	},
	_removeClass: function( el, className ) {
		className.split( ' ' ).forEach( function( str ) {
			if( el.classList ) el.classList.remove( str );
			else el.className = el.className.replace( new RegExp( '\\b' + str + '\\b', 'g' ), '' );
		});
		return el;
	},
	_toggleClass: function( el, className ) {
		if( !this._hasClass( el, className )) this._addClass( el, className );
		else this._removeClass( el, className );
	},
	_isString: function( str ) {
		return Object.prototype.toString.call( str ) === "[object String]";
	},
	_on: function( type, el, handler ) {
		if( !el )return;
		el.addEventListener( type, handler, false );
	},
	_off: function( type, el, handler ) {
		if( !el )return;
		el.removeEventListener( type, handler, false );
	},
	_cssEventEnd: function( eventType ) {
		var el = document.createElement('fakeelement');
		var cssEvents = {
			animation: {
				animation:'animationend',
				WebkitAnimation:'webkitAnimationEnd',
				MozAnimation:'animationend',
				OAnimation:'oAnimationEnd oanimationend',
			},
			transition: {
				transition:'transitionend',
				WebkitTransition:'webkitTransitionEnd',
				MozTransition:'transitionend',
				OTransition:'oTransitionEnd otransitionend',
			},
		};
		if( cssEvents.hasOwnProperty( eventType )) {
			for( var eventEnd in cssEvents[eventType] ) {
				if( el.style[eventEnd] !== undefined ) {
					return cssEvents[eventType][eventEnd];
				}
			}
		}
	},
	// see: https://github.com/angus-c/just#just-extend
	_extend: function()
	{
		var args = [].slice.call( arguments );
		var deep = false;
		if( typeof args[0] === 'boolean' ) {
			deep = args.shift();
		}
		var result = args[0];
		var extenders = args.slice(1);
		var len = extenders.length;
		for( var i = 0; i < len; i++ ) {
			var extender = extenders[i];
			for( var key in extender ) {
				var value = extender[ key ];
				if( deep && value && ( typeof value == 'object' )) {
					var base = Array.isArray( value ) ? [] : {};
					result[ key ] = this._extend( true, base, value );
				}
				else {
					result[ key ] = value;
				}
			}
		}
		return result;
	},
};

(function( ElementPrototype )
{
	// matches polyfill
	ElementPrototype.matches = ElementPrototype.matches ||
	function( selector ) {
		var matches = ( this.document || this.ownerDocument ).querySelectorAll( selector );
		var i = matches.length;
		while( --i >= 0 && matches.item( i ) !== this );
		return i > -1;
	};

	// closest polyfill
	ElementPrototype.closest = ElementPrototype.closest ||
	function( selector ) {
		var el = this;
		while( el.matches && !el.matches( selector )) {
			el = el.parentNode;
		}
		return el.matches ? el : null;
	};
})( Element.prototype );
