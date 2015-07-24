(function($, root){

	"use sctrict";

	var AjaxService = require("./../services/ajaxService.js");

	function CounterController(){
		this.el = $("#counter-holder");
		this.initialize.apply(this, arguments);
	}
	CounterController.prototype = {
		countHolder: $("#count-holder"),
		count: null,
		ajaxService: {},
		initialize: initialize,
		_events: _events,
		handleCounter: handleCounter,
		longPoll: longPoll,
		pad: pad
	}

	function initialize(){
		this._events();
		this.ajaxService = new AjaxService();
		this.longPoll();
	}
	function _events(){
		this.el.on("counterUpdate", $.proxy(this.handleCounter, this));
	}
	function handleCounter(e){
		this.count = this.pad(this.count, 9);
		this.countHolder.html(this.count);
	}
	function longPoll(){
		var self = this;
		
		root.setInterval(function(){
			self.ajaxService.request("/packagesNumber")
			.done(function(response){
				self.count = response.count;
				self.el.trigger("counterUpdate");
			});
		}, 3000);
	}
	function pad(num, size){
		return ("0000000000" + num).substr(-size);
	}

	module.exports = CounterController;

})(jQuery, window);
