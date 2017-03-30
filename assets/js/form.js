;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( selector, options )
	{
		this.el = castor._isString( selector ) ? document.querySelector( selector ) : selector;
		if( this.el ) {
			this.options = castor._extend( this.defaults, options, this.el.getAttribute( 'data-options' ));
			this.init();
		}
	};

	Plugin.prototype =
	{
		defaults: {
			buttonSelector: '[type="submit"]',
			fieldClass: 'form-field',
			fieldErrorsClass: 'field-errors',
			fieldHasErrorClass: 'field-has-error',
			formHasErrorsClass: 'form-has-errors',
			formMessageClass: 'form-messages',
		},

		clearFieldError: function( el )
		{
			var fieldEl = el.closest( '.'+this.options.fieldClass );
			if( fieldEl === null )return;
			castor._removeEl( '.'+this.options.fieldErrorsClass, fieldEl );
			castor._removeClass( fieldEl, this.options.fieldHasErrorClass );
		},

		clearFormErrors: function()
		{
			for( var i = 0; i < this.el.length; i++ ) {
				this.clearFieldError( this.el[i] );
			}
		},

		init: function()
		{
			this.button = this.el.querySelector( this.options.buttonSelector );
			castor._on( 'change', this.el, this.onChange.bind( this ));
			castor._on( 'submit', this.el, this.onSubmit.bind( this ));
		},

		onChange: function( ev )
		{
			this.clearFieldError( ev.target );
		},

		onSubmit: function( ev )
		{
			ev.preventDefault();
			if( !this.button.disabled ) {
				this.button.setAttribute( 'disabled', '' );
			}
			this.submitForm();
		},

		parseFormData: function( convert )
		{
			convert = !!convert || false;
			var keyBreaker = /[^\[\]]+/g; // used to parse bracket notation
			var data = {};
			var seen = {}; // used to uniquely track seen values
			var nestData = function( field, data, parts, seenName )
			{
				var name = parts.shift();
				// Keep track of the dot separated fullname
				seenName = seenName ? seenName+'.'+name : name;
				if( parts.length ) {
					if( !data[name] ) {
						data[name] = {};
					}
					// Recursive call
					nestData( field, data[name], parts, seenName );
				}
				else {
					// Convert the value
					var value = convert ? this._convertValue( field.value ) : field.value;
					// Handle same name case, as well as "last checkbox checked" case
					if( seenName in seen && field.type !== "radio" && !data[name].isArray()) {
						data[name] = ( name in data ) ? [data[name]] : [];
					}
					else {
						seen[seenName] = true;
					}
					// Finally, assign data
					if( this._inArray( field.type, ['radio','checkbox'] ) && !field.checked )return;
					if( !data[name] ) {
						data[name] = value;
					}
					else {
						data[name].push( value );
					}
				}
			}.bind( this );
			for( var i = 0; i < this.el.length; i++ ) {
				var field = this.el[i];
				if( !field.name || field.disabled || this._inArray( field.type, ['file','reset','submit','button'] ))continue;
				var parts = field.name.match( keyBreaker );
				if( !parts.length ) {
					parts = [field.name];
				}
				nestData( field, data, parts );
			}
			return data;
		},

		postAjax: function( data, success )
		{
			var params = typeof data !== 'string' ? this._serialize( data ) : data;
			var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( "Microsoft.XMLHTTP" );
			xhr.open( 'POST', '/ajax.php' ); // asynchronously
			xhr.onreadystatechange = function() {
				if( xhr.readyState > 3 && xhr.status === 200 ) {
					success( JSON.parse( xhr.responseText ));
				}
			};
			xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
			xhr.send( params );
			return xhr;
		},

		showFormErrors: function( errors )
		{
			var fieldEl;
			var errorsEl;
			for( var error in errors ) {
				if( !errors.hasOwnProperty( error ))continue;
				fieldEl  = this.el.querySelector( '[name="' + error + '"]' ).closest( '.'+this.options.fieldClass );
				castor._addClass( fieldEl, this.options.fieldHasErrorClass );
				errorsEl = fieldEl.querySelector( '.'+this.options.fieldErrorsClass );
				if( errorsEl === null ) {
					errorsEl = castor._appendTo( fieldEl, 'span', {
						'class': this.options.fieldErrorsClass,
					});
				}
				for( var i = 0; i < errors[error].errors.length; i++ ) {
					if( errors[error].errors[i] === null )continue;
					errorsEl.innerHTML += '<span>' + errors[error].errors[i] + '</span>';
				}
			}
		},

		showFormMessage: function( response )
		{
			var messageEl = this.el.querySelector( '.'+this.options.formMessageClass );
			if( messageEl === null ) {
				messageEl = castor._insertBefore( this.button, 'div', {
					'class': this.options.formMessageClass,
				});
			}
			castor[!!response.errors ? '_addClass' : '_removeClass']( messageEl, this.options.formHasErrorsClass );
			messageEl.innerHTML = response.message;
		},

		submitForm: function()
		{
			var data = {
				action: 'submit-' + this.el.id,
				request: this.parseFormData(),
			};
			castor._removeEl( '.'+this.options.formMessageClass, this.el );
			this.postAjax( data, function( response ) {
				this.clearFormErrors();
				this.showFormMessage( response );
				if( this.button.disabled ) {
					this.button.removeAttribute( 'disabled' );
				}
				if( !!response.errors ) {
					this.showFormErrors( response.errors );
				}
				else {
					this.el.reset();
				}
			}.bind( this ));
		},

		_convertValue: function( value )
		{
			if( this._isNumeric( value )) {
				return parseFloat( value );
			}
			else if( value === 'true') {
				return true;
			}
			else if( value === 'false' ) {
				return false;
			}
			else if( value === '' || value === null ) {
				return undefined;
			}
			return value;
		},

		_inArray: function( needle, haystack )
		{
			var length = haystack.length;
			while( length-- ) {
				if( haystack[ length ] === needle ) {
					return true;
				}
			}
			return false;
		},

		_isNumeric: function( value )
		{
			return !( isNaN( parseFloat( value )) || !isFinite( value ));
		},

		_serialize: function( obj, prefix )
		{
			var str = [];
			for( var property in obj ) {
				if( !obj.hasOwnProperty( property ))continue;
				var key = prefix ? prefix + "[" + property + "]" : property;
				var value = obj[ property ];
				str.push(
					typeof value === "object" ?
					this._serialize( value, key ) :
					encodeURIComponent( key ) + "=" + encodeURIComponent( value )
				);
			}
			return str.join( "&" );
		},
	};

	Plugin.defaults = Plugin.prototype.defaults;
	castor.Form = Plugin;

})( window, document );
