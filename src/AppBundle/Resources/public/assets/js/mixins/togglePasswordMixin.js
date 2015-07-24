(function($, root){

	"use strict";

	var togglePasswordMixin = {
		togglePassword: togglePassword
	}

	function togglePassword(e){
		e.preventDefault();
		var target = $(e.target).closest(".toggle-password"),
			password = target.closest(".field-holder").find("input.password");

		if(target.hasClass("active")){
			password.attr("type", "password");
			target.removeClass("active");
		} else{
			password.attr("type", "text");	
			target.addClass("active");
		}
	}

	module.exports = togglePasswordMixin;

})(jQuery, window);