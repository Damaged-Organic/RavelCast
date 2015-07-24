var gulp = require("gulp"),
	rename = require("gulp-rename"),
	notify = require("gulp-notify"),

	concat = require("gulp-concat"),
	minifyCSS = require("gulp-minify-css"),
	autoprefixer = require("autoprefixer-core"),
	postcss = require("gulp-postcss"),
	imagemin = require("gulp-imagemin"),
	pngquant = require("imagemin-pngquant"),

	browserify = require("browserify"),
	source = require("vinyl-source-stream"),
	streamify = require("gulp-streamify"),
	uglify = require("gulp-uglify");

gulp.task("scripts", function(){

	var bundle = browserify("assets/js/app.js").bundle();

	bundle
		.pipe(source("bundle.js"))
		.pipe(streamify(uglify()))
		.pipe(rename("bundle.min.js"))
		.pipe(gulp.dest("builds/js"))
		.pipe(notify({message: "scripts bundled"}));
});

gulp.task("css", function(){
	var processors = [
		autoprefixer({
			browsers: ['last 2 versions', 'safari 5', 'ie 9', 'opera 12.1', 'ios 6', 'android 4', 'ff >= 20'],
			cascade: true,
			remove: true,
			add: true
		})
	];

	gulp.src(["assets/css/reset.css", "assets/css/common.css", "assets/css/*.css"])
		.pipe(concat("bundle.css"))
		.pipe(postcss(processors))
		.pipe(minifyCSS())
		.pipe(rename("bundle.min.css"))
		.pipe(gulp.dest("builds/css"))
		.pipe(notify({message: "css bundled"}));
});

gulp.task("fonts", function(){
	gulp.src("assets/fonts/*")
		.pipe(gulp.dest("builds/fonts"))
		.pipe(notify({message: "fonts copied", onLast: true}));
});

gulp.task("images", function(){
	gulp.src("assets/images/*")
		.pipe(imagemin({
			progressive: true,
			optimizationLevel: 4,
	            svgoPlugins: [{removeViewBox: false}],
	            use: [pngquant({quality: '65-80', speed: 4})]
		}))
		.pipe(gulp.dest("builds/images"))
		.pipe(notify({message: "images optimized", onLast: true}));
});

gulp.task("default", ["css", "fonts", "images", "scripts"]);
