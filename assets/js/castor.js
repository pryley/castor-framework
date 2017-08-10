/*jshint curly:false,forin:false*/

AnimationFrame.shim();

window.castor =
{
	_addClasses: function( el, className ) {
		className.split( ' ' ).forEach( function( str ) {
			el.classList.add( str );
		});
		return el;
	},
	_removeClasses: function( el, className ) {
		className.split( ' ' ).forEach( function( str ) {
			el.classList.remove( str );
		});
		return el;
	},
	_isString: function( str ) {
		return Object.prototype.toString.call( str ) === "[object String]";
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
