castor._appendTo = function( el, tag, attributes ) {
	var newEl = castor._createEl( tag, attributes );
	el.appendChild( newEl );
	return newEl;
};

castor._createEl = function( tag, attributes ) {
	var el = ( typeof tag === 'string' ) ? document.createElement( tag ) : tag;
	attributes = attributes || {};
	for( var key in attributes ) {
		if( !attributes.hasOwnProperty( key ))continue;
		el.setAttribute( key, attributes[ key ] );
	}
	return el;
};

castor._insertAfter = function( el, tag, attributes ) {
	var newEl = castor._createEl( tag, attributes );
	el.parentNode.insertBefore( newEl, el.nextSibling );
	return newEl;
};

castor._insertBefore = function( el, tag, attributes ) {
	var newEl = castor._createEl( tag, attributes );
	el.parentNode.insertBefore( newEl, el );
	return newEl;
};

castor._removeEl = function( selector, parent ) {
	var el = parent.querySelector( selector );
	if( el !== null ) {
		el.parentNode.removeChild( el );
	}
};
