(function($, root){

	"use strict";

	function RavelFormFilter(){
		this.initialize.apply(this, arguments);
	}
	RavelFormFilter.prototype = {
		initialize: initialize,
		filter: filter
	}

	function initialize(){

	}
	function filter(dataPackage, formData){
		formData = formData.filter(function(elem){
			return elem.name !== "stashedDataPackage[passPhrase]" && elem.name !== "stashedDataPackage[data]";
		});

		formData.push({
				name:  "stashedDataPackage[hashBeta]",
				value: dataPackage["hashBeta"]
			}, {
				name:  "stashedDataPackage[hashGamma]",
				value: dataPackage["hashGamma"]
			}, {
				name:  'stashedDataPackage[data]',
				value: dataPackage["data"]
			}
		);
		return formData;
	}

	module.exports = RavelFormFilter;

})(jQuery, window);