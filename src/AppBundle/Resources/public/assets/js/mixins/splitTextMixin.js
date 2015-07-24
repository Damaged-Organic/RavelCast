(function($, root){

	"use strict";

	var splitTextMixin = {
		splitText: splitText
	}

	function splitText(message){
		var lines = message.split(/\r?\n/gi),
			html = "", i;

		for(i = 0; i < lines.length; i++){
			if(lines[i].length > 0){
				html += "<p>"+ lines[i] +"</p>";
			}
		}
		return html;
	}

	module.exports = splitTextMixin;

})(jQuery, window);