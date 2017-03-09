;(function( window, document, undefined ) {
	"use strict";

	var Plugin = function( selector, options, photoswipeOptions )
	{
		this.galleries = castor._isString( selector ) ? document.querySelectorAll( selector ) : selector;
		this.imageSize = 'l';
		this.options = options;
		this.photoswipeOptions = photoswipeOptions;
		this.init();
	}

	Plugin.prototype =
	{
		defaults: {
			imageSelector: '.gallery-image',
			captionSelector: 'figcaption',
			thumbnailSrcAttribute: 'data-src',
		},

		init: function()
		{
			var self = this;
			this.config = castor._extend( {}, this.defaults, this.options );
			this.parseHash( this.galleries );
			[].forEach.call( this.galleries, function( gallery, index ) {
				gallery.setAttribute( 'data-pswp-uid', index + 1 );
				castor._on( 'click', gallery, self.onClick.bind( self ));
			});
		},

		beforeResize: function()
		{
			var firstResize = true;
			var imageSrcWillChange;
			var realViewportWidth;
			var dpiRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
			realViewportWidth = this.gallery.viewportSize.x * Math.min( dpiRatio, 2.5 );
			if(( !this.gallery.likelyTouchDevice && realViewportWidth > 800 ) || realViewportWidth >= 1200 || screen.width > 1200 ) {
				if( this.imageSize === 'm' ) {
					this.imageSize = 'l';
					imageSrcWillChange = true;
				}
			}
			else if( this.imageSize === 'l' ) {
				this.imageSize = 'm';
				imageSrcWillChange = true;
			}
			if( imageSrcWillChange && !firstResize ) {
				this.gallery.invalidateCurrItems();
			}
			if( firstResize ) {
				firstResize = false;
			}
			imageSrcWillChange = false;
		},

		getTextForShare: function( shareButtonData ) {
			var text = document.createElement( 'DIV' );
			text.innerHTML = this.gallery.currItem.title || '';
			return text.innerText;
		},

		gettingData: function( index, item )
		{
			item.h = item[this.imageSize].h;
			item.src = item[this.imageSize].src;
			item.w = item[this.imageSize].w;
		},

		getPhotoswipeOptions: function( index, galleryElement, disableAnimation, fromURL )
		{
			return castor._extend({
				galleryUID: galleryElement.getAttribute( 'data-pswp-uid' ),
				getThumbBoundsFn: function( index ) {
					var rect = galleryElement.children[index].children[0].getBoundingClientRect();
					return {
						x: rect.left,
						y: rect.top + window.pageYOffset,
						w: rect.width,
					};
				},
				clickToCloseNonZoomable: false,
				shareButtons: [
					{id:'facebook', label:'Share on Facebook', url:'https://www.facebook.com/sharer/sharer.php?u={{url}}'},
					{id:'twitter', label:'Tweet', url:'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'},
					{id:'pinterest', label:'Pin it', url:'http://www.pinterest.com/pin/create/button/?url={{url}}&media={{image_url}}&description={{text}}'},
				],
				index: fromURL ? parseInt( index, 10 )-1 : parseInt( index, 10 ),
				showAnimationDuration: disableAnimation ? 0 : 1,
			}, this.photoswipeOptions );
		},

		onClick: function( ev )
		{
			var clickedListItem = ev.target.closest( this.config.imageSelector );
			if( !clickedListItem )return;
			var clickedGallery = clickedListItem.parentNode;
			var self = this;
			[].some.call( clickedGallery.children, function( el, index ) {
				if( clickedListItem === el ) {
					self.open( index, clickedGallery );
					return true;
				}
			});
			ev.preventDefault();
		},

		open: function( index, galleryElement, disableAnimation, fromURL )
		{
			var options = this.getPhotoswipeOptions( index, galleryElement, disableAnimation, fromURL );

			// exit if index not found
			if( isNaN( options.index ))return;

			// Initialize PhotoSwipe
			this.gallery = new PhotoSwipe(
				document.querySelectorAll('.pswp')[0],
				PhotoSwipeUI_Default,
				this.parseThumbnails( galleryElement ),
				options
			);

			this.gallery.options.getTextForShare = this.getTextForShare;

			this.gallery.listen( 'beforeResize', this.beforeResize.bind( this ));
			this.gallery.listen( 'gettingData', this.gettingData.bind( this ));
			this.gallery.init();
		},

		parseHash: function( galleryElements )
		{
			var hash = window.location.hash.substring(1);
			var params = {};
			if( hash.length < 5 )return;
			hash.split( '&' ).forEach( function( item, index ) {
				item = item.split( '=' );
				if( item.length === 2 ) {
					params[item[0]] = (item[0] === 'gid') ? parseInt( item[1], 10 ) : item[1];
				}
			});
			if( params.pid && params.gid ) {
				this.open( params.pid,  galleryElements[params.gid - 1], true, true );
			}
		},

		parseThumbnails: function( galleryElement )
		{
			var items = [];
			var self = this;
			[].forEach.call( galleryElement.children, function( el, index ) {
				var img = el.querySelector( 'img' );
				var caption = el.querySelector( self.config.captionSelector );
				var data = JSON.parse( el.getAttribute( 'data-ps' ));
				var item = data.l;
				item.l = data.l;
				item.m = data.m;
				if( img ) {
					item.msrc = img.getAttribute( self.config.thumbnailSrcAttribute ); // thumbnail url
				}
				if( caption ) {
					item.title = caption.innerHTML;
				}
				items.push( item );
			});
			return items;
		},
	};

	Plugin.defaults = Plugin.prototype.defaults;

	castor.PhotoSwipe = Plugin;

})( window, document );
