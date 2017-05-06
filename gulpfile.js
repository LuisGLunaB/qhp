var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var browserSync = require('browser-sync').create();

// browserSync.init({
//     server: "./"
// });


// gulp.task('browser-sync', function() {
//     browserSync.init({
//         proxy: "./backend/"
//     });
// });

gulp.task('default', function() {
  gulp.watch('**/*.php').on('change', function () {
    console.log("Reload PHP");
    browserSync.reload;
  });
  gulp.watch('sass/**/*.scss',['styles']);

  browserSync.init({
      proxy: "localhost/backend",
      port: 80
   });
  // browserSync.stream();

});

gulp.task('styles', function() {
  console.log("Save CSS");
  gulp.src('sass/**/*.scss')
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer({
        browsers: ['last 2 versions']
      }))
      .pipe( browserSync.reload({stream:true}) )
      .pipe(gulp.dest('./css2'));
});
