jq(document).one("ready", function() {
	// Reload library
	jq(document).on("click", "#libRefresh", function() {
		jq("#wDocLib").trigger("saveState");
		jq(this).trigger("reload");
	});
});