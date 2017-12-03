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
                tasks: ['uglify']
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
                files: {
                    '<%= dirs.css %>/dist/amarkal-shortcode-editor.min.css': ['<%= dirs.css %>/src/editor.css'],
                    '<%= dirs.css %>/dist/amarkal-shortcode-popup.min.css': ['<%= dirs.css %>/src/popup.css']
                }
            }
        },
        uglify: {
            main: {
                options: {
                    sourceMap: true,
                    wrap: 'Amarkal'
                },
                files: {
                    '<%= dirs.js %>/dist/amarkal-shortcode.min.js': [
                        '<%= dirs.js %>/src/popup.js',
                        '<%= dirs.js %>/src/placeholder.js',
                        '<%= dirs.js %>/src/shortcode.js',
                        '<%= dirs.js %>/src/core.js'
                    ]
                }
            }
        }
    });
};