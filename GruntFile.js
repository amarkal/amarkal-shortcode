module.exports = function(grunt) {
    'use strict';
    
    // Load npm tasks beginning with 'grunt-'
    require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );
    
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        dirs: {
            css: "assets/css",
            js: "assets/js",
            scss: "assets/scss"
        },
        watch: {
            options: {
                spawn: false // Makes watch run A LOT faster, and also lets you pass variables to the grunt tasks being called
            },
            js: {
                files: ['<%= dirs.js %>/src/*.js'],
                tasks: ['concat:js','uglify']
            },
            scss: {
                files: [
                    '<%= dirs.scss %>/*.scss'
                ],
                tasks: ['compass','concat:css']
            }
        },
        compass: {
            dist: {
                options: {
                    sassDir: '<%= dirs.scss %>',
                    cssDir: '<%= dirs.css %>/src',
                    environment: 'production',
                    raw: 'preferred_syntax = :scss\n', // Use `raw` since it's not directly available
                    outputStyle: 'compressed'
                }
            }
        },
        concat: {
            css: {
                options: {
                    separator: ''
                },
                src: ['<%= dirs.css %>/src/*.css'],
                dest: '<%= dirs.css %>/dist/amarkal-shortcode.min.css'
            },
            js: {
                options: {
                    banner: '(function($,global){',
                    footer: '})(jQuery, window);',
                    separator: "\n"
                },
                src: [
                    '<%= dirs.js %>/src/shortcode.js',
                    '<%= dirs.js %>/src/plugin.js'
                ],
                dest: '<%= dirs.js %>/dist/amarkal-shortcode.min.js'
            }
        },
        uglify: {
            main: {
                options: {
                    banner: ''
                },
                files: {
                    '<%= dirs.js %>/dist/amarkal-shortcode.min.js': ['<%= dirs.js %>/dist/amarkal-shortcode.min.js']
                }
            }
        }
    });
};