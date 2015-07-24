(function($, root){

	"use strict";

	function UnravelFormFilter(){
		this.initialize.apply(this, arguments);
	}
	UnravelFormFilter.prototype = {
		initialize: initialize,
		filter: filter
	}

	function initialize(){

	}
	function filter(honeyPot, hashGamma, formData){
		formData = formData.filter(function(elem){
			return elem.name !== "stashedDataPackage[passPhrase]" && elem.name !== "stashedDataPackage[saltGamma]" && elem.name !== "honeyPot"
		});
		formData.push({
			name: "honeyPot",
			value: honeyPot
		}, {
			name: "stashedDataPackage[hashGamma]",
			value: hashGamma
		});

		return formData;
	}

	module.exports = UnravelFormFilter;

})(jQuery, window);
