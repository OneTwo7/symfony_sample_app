var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var env = process.env.GULP_ENV;
var changed = require('gulp-changed');
var watch = require('gulp-watch');
 
//JAVASCRIPT TASK: write one minified js file out of jquery.js, bootstrap.js and all of my custom js files
gulp.task('js', function () {
    return gulp.src(['vendor/twitter/bootstrap/dist/js/bootstrap.js',
        'app/Resources/js/**/*.js'])
        .pipe(changed('web/js'))
        .pipe(concat('javascript.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'));
});
 
//CSS TASK: write one minified css file out of bootstrap.scss and all of my custom less files
gulp.task('css', function () {
    return gulp.src([
        'vendor/twitter/bootstrap/dist/css/bootstrap.css',
        'app/Resources/scss/**/*.scss'])
        .pipe(changed('web/css'))
        .pipe(gulpif(/[.]scss/, sass()))
        .pipe(concat('styles.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'));
});

gulp.task('watch', function () {
    watch('app/Resources/scss/**/*.scss', function () {
        gulp.start('css');
    });
    watch('app/Resources/js/**/*.js', function () {
        gulp.start('js');
    });
});
 
//define executable tasks when running "gulp" command
gulp.task('default', ['js', 'css', 'watch']);