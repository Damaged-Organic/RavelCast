(function($, root){

	"use strict";

	function ManifestController(){
		this.el = $("#manifest");
		this.initialize.apply(this, arguments);
	}
	
	ManifestController.prototype = {
		button: $("#manifest-button"),
		initialize: initialize,
		_events: _events,
		openManifest: openManifest,
		closeManifest: closeManifest
	}

	function initialize(){
		this._events();
	}
	function _events(){
		this.button.on("click", $.proxy(this.openManifest, this));
		this.el.on("click", $.proxy(this.closeManifest, this));
	}
	function openManifest(e){
		e.preventDefault();
		this.button.addClass("active");
		this.el.addClass("active");
	}
	function closeManifest(e){
		e.preventDefault();
		
		var target = $(e.target);
		if(!target.closest(".inner").length || target.hasClass("close")){
			this.button.removeClass("active");
			this.el.removeClass("active");
		}
	}

	module.exports = ManifestController;

})(jQuery, window);