;(function(factory){

	if(typeof define === "function" && define.amd){
		define(["jquery", "exports"], function($, exports){
			window.sineWave = factory(window, exports, $);
		});
	} else if(typeof exports !== "undefined"){
		
		var $;
		try { $ = require("jquery"); } catch(e) {};

		module.exports = factory(window, exports, $ || window.jQuery);
	} else{
		window.sineWave = factory(window, {}, (window.jQuery || window.$));
	}

}(function(root, sineWave, $){

	root.requestAnimationFrame = root.requestAnimationFrame ||
									root.webkitRequestAnimationFrame ||
									root.mozRequestAnimationFrame ||
									root.msRequestAnimationFrame ||
									root.oRequestAnimationFrame ||
									function(callback){
										root.setTimeout(callback, 1000 / 60)
									}

	sineWave = {
		/* canvas */
		el: $("#sine-wave")[0],
		/* sine options */
		sines: [],
		sineCount: 5,
		/* options */
		centerX: 0,
		centerY: 0,
		amplitude: 15,
		frequency: .75,
		animation: null,
		isFrqTop: false,
		isAmpTop: false,
		isAmp: true,
		/* methods */
		initialize: initialize,
		_events: _events,
		setDimension: setDimension,
		setCenterPosition: setCenterPosition,
		createSineWaves: createSineWaves,
		drawSine: drawSine,
		render: render
	}

	/* methods start */

	function initialize(amp){
		this.isAmp = amp ? false : true;
		this.ctx = this.el.getContext("2d");

		if(!this.isAmp){
			this.amplitude = 2.5;
			this.frequency = .05;
		}

		this._events();
		this.setDimension();
		this.createSineWaves();
		this.render();
	}
	function _events(){
		$(window).on("resize", $.proxy(this.setDimension, this));
	}
	function setDimension(){
		this.el.width = root.innerWidth;
		this.el.height = root.innerHeight;

		this.setCenterPosition();
	}
	function setCenterPosition(){
		this.centerX = this.el.width / 2;
		//extra 2 percent for 768 dimension
		this.centerY = this.el.height / 2 + this.el.height / 100 * 2;
	}
	function createSineWaves(){
		var sine, i;

		for(i = 0; i < this.sineCount; i++){
			sine = new Sine();
			sine.translateY = this.centerY + i * 5;
			sine.translateX = 2;
			sine.amplitude = this.amplitude;
			sine.frequency = this.frequency;
			sine.isChange = this.isAmp;

			this.sines.push(sine);
		}
		this.drawSine();
	}
	function drawSine(){
		var sine, i;

		for(i = 0; i < this.sineCount; i++){
			sine = this.sines[i];

			!this.isFrqTop && sine.frequency <= 1.75 ? sine.frequency += 0.0003 : this.isFrqTop = true;
			this.isFrqTop && sine.frequency >= this.frequency ? sine.frequency -= 0.0003 : this.isFrqTop = false;

			!this.isAmpTop && sine.amplitude <= 95 ? sine.amplitude += 0.002 : this.isAmpTop = true;
			this.isAmpTop && sine.amplitude >= this.amplitude ? sine.amplitude -= 0.002 : this.isAmpTop = false;

			sine.draw(this.el.width, this.ctx);
		}
	}
	function render(){
		animation = root.requestAnimationFrame($.proxy(this.render, this));
		this.ctx.clearRect(0, 0, this.el.width, this.el.height);

		this.drawSine();
	}
	/* methods end */

	function Sine(trX, trY, amp, freq){
		this.translateX = trX || 0;
		this.translateY = trY || 0;
		this.amplitude = amp || 0;
		this.frequency = freq || 0;
		this.isChange = true;
	}
	Sine.prototype = {
		draw: draw,
		getAngle: getAngle
	}

	function draw(dist, ctx){
		var x = 0, y = 0;

		ctx.save();
		ctx.strokeStyle = "#1e93c2";
		ctx.lineWidth = 0.5;

		ctx.translate(this.translateX, this.translateY);
		ctx.beginPath();
		for(x = 0; x < dist; x += 4){
			if(this.isChange){
				y = this.getAngle(x);
			}
			ctx.lineTo(x, y);
		}
		ctx.lineJoin = "round";
		ctx.stroke();
		ctx.restore();
	}
	function getAngle(x){
		return Math.abs(this.amplitude) * Math.sin(this.frequency * x * Math.PI / 180) + Math.abs(this.amplitude) * Math.sin(this.frequency + 1.5 * x * Math.PI / 180);
	}

	return sineWave;

}));