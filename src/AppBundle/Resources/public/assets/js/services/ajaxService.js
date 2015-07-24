(function($, root){

	"use strict";

	function AjaxService(options){
		this._options = options || {};
		this.initialize.apply(this, arguments);
	}
	AjaxService.prototype = {
		initialize: initialize,
		setOptions: setOptions,
		request: request
	}

	function initialize(){
		this.setOptions();
	}
	function setOptions(){
		if(typeof(this._options) !== "object") return;
		$.ajaxSetup(this._options);			
	}
	function request(url, data){
		if(typeof(url) !== "string") return;

		return $.ajax({
			url: url,
			type: "POST",
			data: data || {}
		})			
	}

	module.exports = AjaxService;

})(jQuery, window);