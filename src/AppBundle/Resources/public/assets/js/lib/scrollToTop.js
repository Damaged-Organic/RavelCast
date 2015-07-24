/*(function(root){

	define(["jquery", "jqueryui"], function($){

		function ScrollToTop(){
			this.el = $("#to-top");
			this.initialize.apply(this, arguments);
		}
		ScrollToTop.prototype = {
			initialize: initialize,
			_events: _events,
			handleTop: handleTop
		}

		function initialize(){
			this._events();
		}
		function _events(){
			this.el.on("click", $.proxy(this.handleTop, this));
		}
		function handleTop(e){
			e.preventDefault();
			$("body, html").animate({scrollTop: 0}, 500, "easeInOutQuart");
		}

		return ScrollToTop;
	});

})(window);*/


(function($, root){

	"use strict";

	function ScrollToTop(){
		this.el = $("#to-top");
		this.initialize.apply(this, arguments);
	}
	ScrollToTop.prototype = {
		initialize: initialize,
		_events: _events,
		handleTop: handleTop
	}

	function initialize(){
		this._events();
	}
	function _events(){
		this.el.on("click", $.proxy(this.handleTop, this));
	}
	function handleTop(e){
		e.preventDefault();
		$("body, html").animate({scrollTop: 0}, 500, "easeInOutQuart");
	}

	module.exports = ScrollToTop;

})(jQuery, window);