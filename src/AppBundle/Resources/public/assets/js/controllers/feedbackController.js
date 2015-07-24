(function($, root){

	"use strict";

	var AjaxService = require("./../services/ajaxService.js"),
		errorMixin = require("./../mixins/errorMixin.js");

	function FeedbackController(){
		this.el = $("#feedback");
		this.initialize.apply(this, arguments);
	}

	$.extend(FeedbackController.prototype, errorMixin, {
		form: $("#feedback-form"),
		feedbackResponse: $("#feedback-response"),
		ajaxService: {},
		formData: {},
		initialize: initialize,
		_events: _events,
		handleForm: handleForm,
		handleEndProcess: handleEndProcess,
		sendFeedback: sendFeedback
	});

	function initialize(){
		this._events();
		this.ajaxService = new AjaxService();

		this.form.validate();
	}
	function _events(){
		this.el.on("submit", "#feedback-form", $.proxy(this.handleForm, this))
				.on("click", ".end-process", $.proxy(this.handleEndProcess, this))
				.on("click", ".close-error", $.proxy(this.closeError, this));
	}
	function handleForm(e){
		e.preventDefault();

		if(!this.form.valid()) return;
		this.formData = this.form.serializeArray();
		this.el.addClass("handling");
		this.sendFeedback();
	}
	function handleEndProcess(e){
		e.preventDefault();
		this.el.removeClass("handling done");
	}
	function sendFeedback(){
		var self = this;

		this.ajaxService.request("/feedbackSend", this.formData)
		.done(function(response){
			self.form[0].reset();
			self.el.addClass("done");
			self.feedbackResponse.html(response.message);
		})
		.fail(function(error){
			error = JSON.parse(error.responseText);

			self.setError(error.message);
			self.el.removeClass("handling");
		});
	}

	module.exports = FeedbackController;

})(jQuery, window);
