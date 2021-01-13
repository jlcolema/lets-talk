/*------------------------------
 Gulp
------------------------------*/

// Notes...

/* Plugins
------------------------------*/

// Notes...

var gulp = require('gulp');

var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var sass = require('gulp-sass');

var autoprefixer = require('gulp-autoprefixer');

var imagemin = require('gulp-imagemin');

var browsersync = require('browser-sync');

/* Directories
------------------------------*/

// Notes...

var basePaths = {

	dev:    'src/',
	dist:   'dist/html/wp-content/themes/letstalk/',
	assets: 'dist/html/wp-content/themes/letstalk/assets/'

};

/* Sass
------------------------------*/

// Notes...

gulp.task('sass', function() {

	return gulp.src(basePaths.dev + 'scss/**/*.scss')

	.pipe(sass({

		outputStyle: 'compressed',
		precision: 9

	}).on('error', sass.logError))

	.pipe(autoprefixer({

		// browsers: ['last 2 versions'],
		cascade: false

	}))

	.pipe(gulp.dest(basePaths.assets + 'css'))

	.pipe(browsersync.stream());

});

/* JavaScript
------------------------------*/

// Notes...

gulp.task('js', function() {

	return gulp.src([

		// Libraries

		basePaths.dev + 'js/libraries/jquery.3.5.1.js',

		// Plugins

		// basePaths.dev + 'js/plugins/slick-1.8.0.js',
		// basePaths.dev + 'js/plugins/smooth-scroll-2.2.0.js',
		// basePaths.dev + 'js/plugins/infinite-slide-2.0.1.js',

		// Functions

		basePaths.dev + 'js/functions.js'

	])

	.pipe(concat('scripts.js'))

	// .pipe(rename({

		// suffix: '.min'

	// }))

	.pipe(uglify())

	.pipe(gulp.dest(basePaths.assets + 'js'))

	.pipe(browsersync.stream());

});

/* Images
------------------------------*/

// Notes...

gulp.task('img', function() {

	return gulp.src(basePaths.dev + 'img/**/*')

	.pipe(imagemin({

		optimizationLevel: 5,
		progressive: true,
		interlaced: true

	}))

	.pipe(gulp.dest(basePaths.assets + 'img'));

});

/* Fonts
------------------------------*/

// Notes...

gulp.task('fonts', function() {

	return gulp.src(basePaths.dev + 'fonts/**/*')

	.pipe(gulp.dest(basePaths.assets + 'fonts'));

});

/* Watch
------------------------------*/

// Notes...

gulp.task('watch', function() {

	browsersync.init({

		proxy: "https://letstalk.local",
		host: "letstalk.local",
		open: "external",
		https: {
			// browser-sync will pick these settings up and use them instead of its self-signed SSL cert for localhost
			key: '/Applications/MAMP/Library/OpenSSL/certs/letstalk.local.key',
			cert: '/Applications/MAMP/Library/OpenSSL/certs/letstalk.local.crt',
		  },
		// browser: 'microsoft edge'

	});

	gulp.watch(basePaths.dev + 'scss/**/*.scss', gulp.series('sass'));

	gulp.watch(basePaths.dev + 'js/*.js', gulp.series('js'));

	gulp.watch(basePaths.dev + 'img/**/*', gulp.series('img'));

	gulp.watch(basePaths.dev + 'fonts/**/*', gulp.series('fonts'));

	gulp.watch(basePaths.dist + '**/*.php').on('change', browsersync.reload);

	gulp.watch(basePaths.dist + '**/*.twig').on('change', browsersync.reload);

});

/* Title
------------------------------*/

// Notes...

gulp.task('default', gulp.parallel('sass', 'js', 'img', 'fonts', 'watch'));