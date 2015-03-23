/**
* Gruntfile.js
* Charcoal-Core configuration for grunt. (The JavaScript Task Runner)
*/

module.exports = function(grunt) {
	"use strict";

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		"yaml-validate": {
			options: {
				glob: ".travis.yml"
			}
		},

		jsonlint:{
			meta:{
				src:[
					'*.json'
				]
			}
		},

		phplint:{
			options: {
				swapPath: '/tmp',
				phpArgs : {
					// add -f for fatal errors
					'-lf': null
				}
			},

			src: [
				'src/**/*.php'
			],
			tests: [
				'tests/**/*.php'
			]
		},

		phpunit:{

			src: {
				dir: 'tests/'
			},

			options: {
				colors: true,
				coverageHtml:'tests/tmp/report/',
				coverageText:'tests/tmp/report/',
				testdoxHtml:'tests/tmp/testdox.html',
				testdoxText:'tests/tmp/testdox.text',
				strict:true,
				verbose:true,
				debug:false,
				bootstrap:'tests/bootstrap.php'
			}
		},

		phpcs: {
			src:{
				dir:[
					'src/**/*.php'
				]
			},
			tests: {
				dir:[
					'tests/**/*.php'
				]
			},
			options: {
				//bin: '<%= directories.composerBin %>/phpcs',
				standard: 'phpcs.xml',
				//ignore: 'database',
				extensions: 'php',
				showSniffCodes: true
			}
		},

		phpdocumentor: {
			dist: {
				options: {
					config: 'phpdoc.dist.xml',
					directory : ['src/', 'tests/'],
					target : 'phpdoc/'
				}
			}
		},
		watch: {
			php: {
				files :[
					'src/**/*.php',
					'tests/**/*.php',
				],
				tasks: ['phplint']
			}
		},
		githooks: {
			all: {
				'pre-commit': 'jsonlint phplint phpunit phpcs phpdocumentor',
			}
		}
		
	});

	// Load plugin(s)
	grunt.loadNpmTasks('grunt-yaml-validate');
	grunt.loadNpmTasks('grunt-jsonlint');
	grunt.loadNpmTasks("grunt-phplint");
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.loadNpmTasks('grunt-phpcs');
	grunt.loadNpmTasks('grunt-phpdocumentor');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-githooks');
	grunt.loadNpmTasks('grunt-composer');	

	// Register Task(s)
	grunt.registerTask('default', [
		'jsonlint',
		'phpunit',
		//'phplint' // To slow for default
	]);
	grunt.registerTask('tests', [
		'phpunit',
		'phplint'
	]);
	
};

