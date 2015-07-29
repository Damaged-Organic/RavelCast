(function(root){

	"use strict";

	function PanelsController(){
		this.initalize.apply(this, arguments);
	}
	PanelsController.prototype = {
		el: $("#panel-holder"),
		content: $("#content"),
		initalize: initalize,
		_events: _events,
		handlePanel: handlePanel,
		switchPanel: switchPanel,
		handlePanelClose: handlePanelClose
	}

	function initalize(){
		this._events();
	}
	function _events(){
		this.el.on("click", ".button, .switch-panel", $.proxy(this.handlePanel, this))
			    .on("click", ".close-panel", $.proxy(this.handlePanelClose, this));
	}
	function handlePanel(e){
		e.preventDefault();

		var target = $(e.target).closest(".switch-panel");
		this.switchPanel(target.data("switch"));
	}
	function handlePanelClose(e){
		var target = $(e.target).closest("#panel-holder");

		target.hasClass("left") ? this.el.removeClass("left") : this.el.removeClass("right");
		if(this.content.hasClass("active")) this.content.removeClass("active");
	}
	function switchPanel(which){
		which === "left" ? this.el.removeClass("right").addClass("left") : this.el.removeClass("left").addClass("right");
		if(!this.content.hasClass("active")) this.content.addClass("active");
	}

	module.exports = PanelsController;

})(window);

