var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');

// 合并全局JS文件
gulp.task('default',function(){
    return gulp.src(['./js/lib/jquery/jquery-1.11.2.min.js', './js/lib/placeholder.js', './js/lib/overall.js','./js/lib/seajs/sea.js','./js/lib/seajs/sea-config.js','./js/lib/crossdomain.js','./js/lib/analysis.js'])
        .pipe(uglify())
        .pipe(concat('global.js'))
        .pipe(gulp.dest('./js/lib/'));
});

// 合并IE8兼容文件
gulp.task('comboCompatible',function(){
    return gulp.src(['./js/lib/html5shiv.min.js', './js/lib/respond.min.js', './js/lib/json.min.js','./js/lib/es5_safe.min.js'])
        .pipe(uglify())
        .pipe(concat('compatible.js'))
        .pipe(gulp.dest('./js/lib/'));
});