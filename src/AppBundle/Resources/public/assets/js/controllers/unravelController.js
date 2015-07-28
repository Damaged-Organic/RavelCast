(function($, root){

	"use strict";

	var	AjaxService = require("./../services/ajaxService.js"),
		UnravelFormFilter = require("./../filters/unravelFormFilter.js"),
		splitTextMixin = require("./../mixins/splitTextMixin.js"),
		togglePasswordMixin = require("./../mixins/togglePasswordMixin.js"),
		errorMixin = require("./../mixins/errorMixin.js"),

		ZeroClipBoard = require("zeroclipboard"),
		TwinBcrypt = require("twin-bcrypt"),
		CryptoJS = require("crypto-js"),

		_WEIGHT = "2y",
		_COST = 10,
		saltCleaner = /(^\s?\[)|(\]\s?$)/g;

	function UnravelController(){
		this.el = $("#unravel");
		this.initialize.apply(this, arguments);
	}
	$.extend(UnravelController.prototype, togglePasswordMixin, errorMixin, {
		form: $("#unravel-form"),
		responseHolder: $("#unravel-message"),
		honeyPotField: $("#un-honey-pot"),
		copyButton: $("#unravel-clipboard-button"),
		formData: {},
		passPhrase: "",
		ajaxService: {},
		unravelFormFilter: {},
		salts: [],
		hashes: [],
		initialize: initialize,
		_events: _events,
		validateForm: validateForm,
		handleForm: handleForm,
		handleCopy: handleCopy,
		handleProcessEnd: handleProcessEnd,
		generateHoneyPot: generateHoneyPot,
		generateHashGamma: generateHashGamma,
		validateHashGamma: validateHashGamma,
		generateHashBeta: generateHashBeta,
		validateHashBeta: validateHashBeta,
		generateHashAlpha: generateHashAlpha,
		unpackMessage: unpackMessage
	});

	function initialize(){
		this._events();
		this.ajaxService = new AjaxService();
		this.unravelFormFilter = new UnravelFormFilter();

		this.validateForm();
		new ZeroClipBoard(this.copyButton);
	}
	function _events(){
		this.el.on("submit", "#unravel-form", $.proxy(this.handleForm, this))
				.on("click", ".toggle-password", $.proxy(this.togglePassword, this))
				.on("click", ".copy", $.proxy(this.handleCopy, this))
				.on("click", ".end-processing", $.proxy(this.handleProcessEnd, this))
				.on("click", ".close-error", $.proxy(this.closeError, this));
	}
	function validateForm(){
		
		$.validator.addMethod("regex", function(value, el, params){
			var regex = new RegExp(params, "gi");
			return regex.test(value);
		}, "wrong hash");

		this.form.validate();
	}
	function handleForm(e){
		e.preventDefault();
		if(!this.form.valid()) return;

		this.formData = this.form.serializeArray();
		this.passPhrase = this.formData[0].value;

		this.el.addClass("handling");
		this.generateHoneyPot();
	}
	function handleCopy(e){
		e.preventDefault();
		this.el.addClass("copied");
	}
	function handleProcessEnd(e){
		this.el.removeClass("handling done copied scrollable");
		this.responseHolder.empty();
	}
	function generateHoneyPot(){
		var self = this;

		this.ajaxService.request("/requestHoneyPot")
		.done(function(response){
			self.honeyPotField.val(response.honeyPot);
			self.generateHashGamma();
		});
	}
	function generateHashGamma(){
		var salt = this.formData[1].value.replace(saltCleaner, "");

		try{
			this.hashes["gamma"] = TwinBcrypt.hashSync(this.passPhrase, "$" + _WEIGHT + "$" + _COST + "$" + salt);
		} catch(e){
			this.setError("Crypt error has occured, try again");
			this.el.removeClass("processing");
			this.form[0].reset();
		}
		this.validateHashGamma();
	}
	function validateHashGamma(){
		var self = this,
			honeyPot = this.honeyPotField.val();

		this.formData = this.unravelFormFilter.filter(honeyPot, this.hashes["gamma"], this.formData);

		this.ajaxService.request("/validateGamma", this.formData)
		.done(function(response){
			self.salts["beta"] = response.saltBeta;
			self.generateHashBeta();
		})
		.fail(function(error){
			error = JSON.parse(error.responseText);

			self.setError(error.message);
			self.el.removeClass("handling");
		});
	}
	function generateHashBeta(){
		try{
			this.hashes["beta"] = TwinBcrypt.hashSync(this.passPhrase, "$" + _WEIGHT + "$" + _COST + "$" + this.salts["beta"]);
		} catch(e){
			this.setError("Crypt error has occured, try again");
			this.el.removeClass("handling");
			this.form[0].reset();
		}	
		this.validateHashBeta();
	}
	function validateHashBeta(){
		var self = this,
			cipherData = "";

		this.ajaxService.request("/validateBeta", {hashBeta: this.hashes["beta"]})
		.done(function(response){
			cipherData = response.cipherData;
			self.salts["alpha"] = response.saltAlpha;
			self.hashes["alpha"] = self.generateHashAlpha();

			self.unpackMessage(cipherData);
		})
		.fail(function(error){
			error = JSON.parse(error.responseText);

			self.setError(error.message);
			self.el.removeClass("handling");
		});
	}
	function generateHashAlpha(){
		var hashAlpha = "";

		try{
			hashAlpha = TwinBcrypt.hashSync(this.passPhrase, "$" + _WEIGHT + "$" + _COST + "$" + this.salts["alpha"]);
		} catch(e){
			this.setError("Crypt error has occured, try again");
			this.el.removeClass("handling");
			this.form[0].reset();
		}	
		return hashAlpha;
	}
	function unpackMessage(cipherData){
		var key = CryptoJS.enc.Base64.parse(this.hashes["alpha"].substr(-22)),
      			iv  = CryptoJS.enc.Base64.parse(this.salts["alpha"]),
      			message = "";

      		try{
      			message = CryptoJS.AES.decrypt(cipherData, key, { iv: iv });
      		} catch(e){
      			this.setError("Crypt error has occured, try again");
			this.el.removeClass("handling");
			this.form[0].reset();
      		}
      		message = message.toString(CryptoJS.enc.Utf8);

      		this.form[0].reset();
      		this.passPhrase = "";

      		if(message.length >= 800) this.el.addClass("scrollable");

      		this.el.addClass("done");
		this.responseHolder.html(message);
	}

	module.exports = UnravelController;

})(jQuery, window);

