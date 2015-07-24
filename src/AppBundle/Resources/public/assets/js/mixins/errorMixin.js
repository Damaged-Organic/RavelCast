(function($, root){

	"use strict";

	var errorMixin = {
		setError: setError,
		closeError: closeError
	}
	function setError(error){
		this.el.find(".error-holder").html("\
			<p>"+ error +"</p>\
			<span class='icon icon-close close-error'></span>\
		").addClass("active");
	}
	function closeError(e){
		e.preventDefault();
		this.el.find(".error-holder").html("").removeClass("active");
	}

	module.exports = errorMixin;

})(jQuery, window);