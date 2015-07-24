(function(root){

	"use script";

	root.$ = root.jQuery = require("jquery");

	require("jquery-ui");
	require("jquery-validation");

	$(function(){
		require("./main.js");
	});

})(window);
