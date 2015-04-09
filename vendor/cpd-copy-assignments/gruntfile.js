(function () {
   'use strict';
}());
module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
			},
			default: {
				files: {
					'assets/js/scripts.min.js': ['assets/js/scripts.js']
				}
			}
		},

		compassMultiple: {
			default: {
				options: {
					sassDir: 'assets/sass',
					cssDir: 'assets/css',
					environment: 'development',
					outputStyle: 'compressed'
				}
			}
		},

		watch: {
			default: {
				files: [
					'assets/**/*.scss',
					'assets/js/**/*.js'
				],
				tasks: ['uglify', 'compassMultiple']
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-compass-multiple');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.registerTask('default', ['uglify', 'compassMultiple', 'watch']);
	grunt.registerTask('build', ['uglify', 'compassMultiple']);
};
