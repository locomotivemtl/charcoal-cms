/**
* @file Charcoal CMS Task Runner for Grunt
*/

module.exports = function(grunt) {
    'use strict';

    function loadConfig(path) {
        var glob = require('glob');
        var object = {};
        var key;

        glob.sync('*.js', {cwd: path}).forEach(function(option) {
            key = option.replace(/\.js$/,'');
            object[key] = require(path + option);
        });

        return object;
    }

    var config = {
        pkg: grunt.file.readJSON('package.json')
    };

    grunt.loadTasks('grunt_tasks');
    grunt.util._.extend(config, loadConfig('./grunt_tasks/'));
    grunt.initConfig(config);

    // Load tasks
    require('load-grunt-tasks')(grunt);

    // Register Task(s)
    grunt.registerTask('default', [
        'phplint',
        'phpunit'
    ]);
    grunt.registerTask('tests', [
        'phplint',
        'phpunit'
    ]);
};
