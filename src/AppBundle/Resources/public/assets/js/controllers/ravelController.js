(function($, root){

	"use strict";

	var	AjaxService = require("./../services/ajaxService.js"),
		RavelFormFilter = require("./../filters/ravelFormFilter.js"),
		splitTextMixin = require("./../mixins/splitTextMixin.js"),
		togglePasswordMixin = require("./../mixins/togglePasswordMixin.js"),
		errorMixin = require("./../mixins/errorMixin.js"),

		ZeroClipBoard = require("zeroclipboard"),
		TwinBcrypt = require("twin-bcrypt"),
		CryptoJS = require("crypto-js"),

		_WEIGHT = "2y",
		_COST = 10;

		function RavelController(){
			this.el = $("#ravel");
			this.initialize.apply(this, arguments);
		}

		$.extend(RavelController.prototype, splitTextMixin, togglePasswordMixin, errorMixin, {
			form: $("#ravel-form"),
			responseHolder: $("#ravel-response"),
			copyButton: $("#ravel-clipboard-button"),
			formData: {},
			salts: [],
			hashes: [],
			ajaxService: {},
			ravelFormFilter: {},
			initialize: initialize,
			_events: _events,
			handleForm: handleForm,
			handleCopy: handleCopy,
			handleProcessEnd: handleProcessEnd,
			generateHoneyPot: generateHoneyPot,
			generateSalt: generateSalt,
			generateHash: generateHash,
			moldPackage: moldPackage,
			sendPackage: sendPackage
		});
		
		function initialize(){
			this._events();
			this.ajaxService = new AjaxService();
			this.ravelFormFilter = new RavelFormFilter();

			this.form.validate();
			new ZeroClipBoard(this.copyButton);
		}
		function _events(){
			this.el.on("submit", "#ravel-form", $.proxy(this.handleForm, this))
					.on("click", ".toggle-password", $.proxy(this.togglePassword, this))
					.on("click", ".copy", $.proxy(this.handleCopy, this))
					.on("click", ".end-processing", $.proxy(this.handleProcessEnd, this))
					.on("click", ".close-error", $.proxy(this.closeError, this));
		}
		function handleForm(e){
			e.preventDefault();
			if(!this.form.valid()) return;

			this.formData = this.form.serializeArray();
			this.el.addClass("handling");
			this.generateHoneyPot();
		}
		function handleCopy(e){
			e.preventDefault();
			this.el.addClass("copied");
		}
		function handleProcessEnd(e){
			this.el.removeClass("handling done copied");
			this.responseHolder.empty();
		}
		function generateHoneyPot(){
			var self = this,
				honeyPotField = $("#honey-pot");

			this.ajaxService.request("/requestHoneyPot")
			.done(function(response){
				honeyPotField.val(response.honeyPot);
				self.generateSalt(response.honeyPot);
			});
		}
		function generateSalt(honeyPot){
			var self = this;

			this.ajaxService.request("/requestSalts", { honeyPot: honeyPot })
			.done(function(response){
				self.salts = response.salts;
				self.generateHash();
			})
			.fail(function(error){
				error = JSON.parse(error.responseText);
				self.setError(error.message);
				self.el.removeClass("handling");
			});
		}
		function generateHash(){
			var password = this.formData[0].value,
				tempSalts = {},
				key;

			for(key in this.salts){
				tempSalts[key] = "$" + _WEIGHT + "$" + _COST + "$" + this.salts[key];
				try{
					this.hashes[key] = TwinBcrypt.hashSync(password, tempSalts[key]);
				} catch(e){
					self.setError("Crypt error has occured, try again");
					self.el.removeClass("handling");
					self.form[0].reset();
				}	
			}
			this.moldPackage();
		}
		function moldPackage(){
			var iv  = CryptoJS.enc.Base64.parse(this.salts["alpha"]),
				key = CryptoJS.enc.Base64.parse(this.hashes["alpha"].substr(-22)),
				message = this.splitText(this.formData[1].value),
				cipherData = {},
				dataPackage = {};

			try{
				cipherData = CryptoJS.AES.encrypt(message, key, { iv: iv });
			} catch(e){
				self.setError("Crypt error has occured, try again");
				self.el.removeClass("handling");
				self.form[0].reset();
			}

			dataPackage = {
				"data": cipherData.toString(),
				"hashBeta": this.hashes["beta"],
				"hashGamma": this.hashes["gamma"]
			}
			this.formData = this.ravelFormFilter.filter(dataPackage, this.formData);
			this.sendPackage();
		}
		function sendPackage(){
			var self = this;

			this.ajaxService.request("/stashData", this.formData)
			.done(function(response){
				self.form[0].reset();
				self.el.addClass("done");

				self.responseHolder.html("<p id='clipboard-salt'>["+ self.salts["gamma"] +"]</p>");
			})
			.fail(function(error){
				error = JSON.parse(error.responseText);
				self.setError(error.message);
				self.el.removeClass("handling");
			});
		}

		module.exports = RavelController;

})(jQuery, window);

