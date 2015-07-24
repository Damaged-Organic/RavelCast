(function($, root){

	"use strict";

	var	ManifestController = require("./controllers/manifestController.js"),
		PanelsController = require("./controllers/panelsController.js"),
		RavelController = require("./controllers/ravelController.js"),
		UnravelController = require("./controllers/unravelController.js"),
		CounterController = require("./controllers/counterController.js"),
		RecommendationController = require("./controllers/recommendationController.js"),
		FeedbackController = require("./controllers/feedbackController.js"),
		ScrollToTop = require("./lib/scrollToTop.js"),
		sineWave = require("./lib/sine.js");

	function Boot(){
		this.initialize.apply(this, arguments);
	}
	Boot.prototype.initialize = function(){
		$(function(){

			new ManifestController();
			new PanelsController();
			new RavelController();
			new UnravelController();
			new CounterController();
			new RecommendationController();
			new FeedbackController();
			new ScrollToTop();

			sineWave.initialize()
		});
	}

	module.exports = new Boot();

})(jQuery, window);
