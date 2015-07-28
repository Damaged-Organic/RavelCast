(function($, root){

	"use strict";

	var errorMixin = {
		setError: setError,
		closeError: closeError,
		autoClose: autoClose
	}
	function setError(error){
		this.el.find(".error-holder").html("\
			<p>"+ error +"</p>\
			<span class='icon icon-close close-error'></span>\
		").addClass("active");

		this.autoClose();
	}
	function closeError(e){
		e.preventDefault();
		this.el.find(".error-holder").html("").removeClass("active");
	}
	function autoClose(){
		var self = this;

		root.setTimeout(function () {
			self.el.find(".error-holder").html("").removeClass("active");
		}, 10000);
	}

	module.exports = errorMixin;

})(jQuery, window);