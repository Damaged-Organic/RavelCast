(function($, root){

	"use strict";

	function RecommendationController(){
		this.el = $("#recommendation");
		this.initialize.apply(this, arguments);
	}
	RecommendationController.prototype = {
		button: $("#recommendation-button"),
		initialize: initialize,
		_events: _events,
		openReccomendations: openReccomendations,
		closeReccomendations: closeReccomendations
	}

	function initialize(){
		this._events();
	}
	function _events(){
		this.button.on("click", $.proxy(this.openReccomendations, this));
		this.el.on("click", $.proxy(this.closeReccomendations, this));
	}
	function openReccomendations(e){
		e.preventDefault();
		this.el.addClass("active");
	}
	function closeReccomendations(e){
		var target = $(e.target);
		if(!target.closest(".inner").length || target.hasClass("close")) this.el.removeClass("active");
	}

	module.exports = RecommendationController;

})(jQuery, window);
