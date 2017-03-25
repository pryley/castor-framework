/*jshint curly:false,forin:false*/

AnimationFrame.shim();

window.castor =
{
	// castor._hasClass
	_hasClass: function( el, className ) {
		if( el.classList ) return el.classList.contains( className );
		else return new RegExp( '\\b' + className + '\\b' ).test( el.className );
	},
	// castor._addClass
	_addClass: function( el, className ) {
		if( el.classList ) el.classList.add( className );
		else if( !this._hasClass( el, className )) el.className += ' ' + className;
	},
	// castor._removeClass
	_removeClass: function( el, className ) {
		if( el.classList ) el.classList.remove( className );
		else el.className = el.className.replace( new RegExp( '\\b' + className + '\\b', 'g' ), '' );
	},
	// castor._toggleClass
	_toggleClass: function( el, className ) {
		if( !this._hasClass( el, className )) this._addClass( el, className );
		else this._removeClass( el, className );
	},
	// castor._isString
	_isString: function( str ) {
		return Object.prototype.toString.call( str ) === "[object String]";
	},
	// castor._on
	_on: function( type, el, handler ) {
		if( !el )return;
		el.addEventListener( type, handler, false );
	},
	// castor._off
	_off: function( type, el, handler ) {
		if( !el )return;
		el.removeEventListener( type, handler, false );
	},
	// castor._transitionEvent
	_transitionEvent: function() {
		var transition;
		var el = document.createElement('fakeelement');
		var transitions = {
			'transition':'transitionend',
			'OTransition':'oTransitionEnd',
			'MozTransition':'transitionend',
			'WebkitTransition':'webkitTransitionEnd',
		};
		for( transition in transitions ) {
			if( el.style[transition] !== undefined ) {
				return transitions[transition];
			}
		}
	},
	// castor._extend
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

(function creditsLog() {
	var css = function( args ) {
		var string = "font-size:{0};color:{1};padding:{2};line-height:24px;text-shadow:0 1px #000;background:#263238;text-decoration:none;";
		return string.replace( /{(\d+)}/g, function( match, number ) {
			return args[ number ];
		});
	};
	console.log(
		"\n%cSite made with %c♥ %cby Paul Ryley\n%chttps://twitter.com/pryley\n%chttps://geminilabs.io%c\n\n",
		css(['12px', '#fff', '8px 0 8px 10px']),
		css(['12px', '#f44336', '8px 0']),
		css(['12px', '#fff', '8px 10px 8px 0']),
		css(['11px', '#90caf9', '5px']),
		css(['11px', '#90caf9', '8px 5px']),
		""
	);
}());
