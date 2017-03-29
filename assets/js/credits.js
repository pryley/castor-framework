(function() {
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
