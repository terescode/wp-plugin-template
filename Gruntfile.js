/* eslint-env node */
/**
 * Grunt wrapper
 */
module.exports = function (grunt) {
  'use strict';

  /**
   * Custom Code
   */

  var fs = require('fs'),
    util = require('util'),
    _ = require('lodash'),
    multitasker = require('grunt-multitasker')(grunt),
    xml2js = require('xml2js'),
    webpack = require('webpack'),
    webpackConfig = require("./webpack.config.js");

  function runFn(callback) {
    return function () {
      var options = this.options({}),
        opts = _.extend({
          stdio: 'inherit'
        }, options.opts);
      grunt.util.spawn({
        cmd: options.cmd,
        args: options.args.concat(this.args),
        opts: opts
      }, callback(this.async()));
    };
  }

  function runPhpWithXdebug(callback) {
    return function () {
      var options = this.options({}),
        php = options.php,
        phpUnit = options.cmd,
        phpUnitArgs = options.args.concat(this.args),
        xDebugPath = options.xDebugPath,
        tmpPath = options.tmpPath,
        zendExtension = '\nzend_extension=' + xDebugPath + '\n',
        done = callback(this.async());
      grunt.util.spawn({
        cmd: php,
        args: [
          '-r',
          'echo php_ini_loaded_file();'
        ]
      }, function (err, result, code) {
        var iniPath;
        if (err || 0 !== code) {
          grunt.log.writeln(util.inspect(result, false, null));
          done(err);
          return;
        }

        iniPath = String(result).trim();
        if (!iniPath) {
          grunt.file.write(tmpPath, zendExtension);
        } else {
          grunt.file.copy(iniPath, tmpPath, {
            process: function (contents) {
              return contents + zendExtension;
            }
          });
        }

        grunt.util.spawn({
          cmd: php,
          args: [
            phpUnit
          ].concat(phpUnitArgs),
          opts: _.extend({
            stdio: 'inherit',
            env: { 'PHPRC': tmpPath }
          }, options.opts)
        }, function (err, result, code) {
          grunt.file.delete(tmpPath, { force: true });
          done(err, result, code);
        });
      });
    };
  }

  function phpmdCompleted(done) {
    return function (error, result, code) {
      if (error || 0 !== code) {
        grunt.log.error('\u2714 FAILED: What a mess! Please review the following issues (reports/md.xml).');
        grunt.log.writeln();
        fs.readFile('./reports/md.xml', function (err, data) {
          if (err) {
            done(err);
          } else {
            grunt.log.writeln();
            grunt.log.errorlns(data);
            grunt.log.writeln();
            done(false);
          }
        });
      } else {
        grunt.log.ok('\u2714 PASSED: Good job, no mess detected!');
        done();
      }
    };
  }

  function phpUnitCompleted(done) {
    return function (error, result, code) {
      if (error || 0 !== code) {
        grunt.log.writeln();
        grunt.log.error('\n\u2714 FAILED: phpunit returned non-zero result: ' + result + ' (' + code + ')');
        grunt.log.writeln();
        done(false);
      } else {
        grunt.log.ok('\u2714 PASSED: Good job, all tests passed!');
        grunt.log.writeln();
        done();
      }
    };
  }

  function checkCoverage(done) {
    var parser = new xml2js.Parser();
    grunt.log.writeln('\nAnalyzing code coverage in ./reports/coverage.xml...');
    fs.readFile('./reports/coverage.xml', function (err, data) {
      if (err) {
        done(err);
      } else {
        parser.parseString(data, function (err, result) {
          if (err) {
            done(err);
          } else {
            if (result.coverage &&
                result.coverage.project &&
                0 < result.coverage.project.length &&
                result.coverage.project[0].metrics &&
                0 < result.coverage.project[0].metrics.length &&
                result.coverage.project[0].metrics[0].$) {
              var metrics = result.coverage.project[0].metrics[0].$,
                methods = metrics.methods,
                coveredMethods = metrics.coveredmethods,
                statements = metrics.statements,
                coveredStatements = metrics.coveredstatements,
                elements = metrics.elements,
                coveredElements = metrics.coveredelements;
              if (methods !== coveredMethods &&
                  statements !== coveredStatements &&
                  elements !== coveredElements) {
                grunt.log.error('\u2714 FAILED: Code coverage is not within acceptable tolerances.');
                grunt.log.writeln();
                grunt.log.writeln(grunt.log.table(
                  [20, 10, 10, 10],
                  ['TYPE', 'FOUND', 'COVERED', 'PERCENTAGE']
                ));
                grunt.log.writeln(grunt.log.table(
                  [20, 10, 10, 10],
                  ['METHODS', methods, coveredMethods, Math.round((coveredMethods / methods) * 100) + '%']
                ));
                grunt.log.writeln(grunt.log.table(
                  [20, 10, 10, 10],
                  ['STATEMENTS', statements, coveredStatements, Math.round((coveredStatements / statements) * 100) + '%']
                ));
                grunt.log.writeln(grunt.log.table(
                  [20, 10, 10, 10],
                  ['ELEMENTS', elements, coveredElements, Math.round((coveredElements / elements) * 100) + '%']
                ));
                grunt.log.writeln();
                grunt.log.error('See ./reports/coverage.xml for details.');
                grunt.log.writeln();
                done(false);
              } else {
                grunt.log.ok('\u2714 PASSED: Good job, 100% code covered!');
                grunt.log.writeln();
                done();
              }
            } else {
              grunt.log.error('\u2714 FAILED: Unexpected coverage data.');
              grunt.log.errorlns(util.inspect(result, false, null));
              grunt.log.writeln();
              done(false);
            }
          }
        });
      }
    });
  }

  /**
   * Grunt task configurations
   */
  grunt.initConfig({

    // load the package.json metadata
    pkg: grunt.file.readJSON('package.json'),

    // Configure phpcs task
    phpcs: {
      application: {
        src: ['*.php', 'includes/*.php', 'admin/*.php', 'public/*.php']
      },
      options: {
        bin: 'vendor/bin/phpcs'
      }
    },

    // Define phpmd command
    'phpmd': {
      application: {
        options: {
          cmd: 'vendor/bin/phpmd',
          args: [
            './',
            'xml',
            'cleancode,codesize,design,naming,unusedcode',
            '--exclude',
            'node_modules,vendor,tests',
            '--reportfile',
            'reports/md.xml'
          ]
        }
      }
    },

    // Define phpunit command
    phpunit: {
      application: {
        options: {
          cmd: 'vendor/bin/phpunit',
          args: [
            '-c',
            'phpunit.xml'
          ]
        }
      }
    },

    'phpunit-cov': {
      application: {
        options: {
          php: '/usr/bin/php',
          cmd: 'vendor/bin/phpunit',
          tmpPath: '/tmp/php.ini',
          xDebugPath: '/usr/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so',
          args: [
            '-c',
            'phpunit.xml.dist'
          ]
        }
      }
    },

    // Configure webpack
    webpack: {
      options: webpackConfig,
      build: {
        plugins: webpackConfig.plugins.concat(
          new webpack.DefinePlugin({
            "process.env": {
              "NODE_ENV": JSON.stringify("production")
            }
          }),
          new webpack.optimize.UglifyJsPlugin({
            compress: {
              warnings: false
            },
            output: {
              comments: false
            }
          })
        )
      },
      "build-dev": {
        devtool: "sourcemap",
        debug: true
      }
    },

    // Configure webpack-dev-server
    'webpack-dev-server': {
      options: {
        webpack: webpackConfig,
        publicPath: "/" + webpackConfig.output.publicPath
      },
      start: {
        keepAlive: true,
        webpack: {
          devtool: "eval",
          debug: true
        }
      }
    },

    watch: {
      app: {
        files: [ "admin/js/**" ],
        tasks: [ "webpack:build-dev" ],
        options: {
          spawn: false
        }
      }
    },

    // Configure the WordPress i18n makepot task
    makepot: {
      target: {
        options: {
          domainPath: '/languages', // Where to save the POT file.
          exclude: [], // List of files or directories to ignore.
          include: [], // List of files or directories to include.
          mainFile: 'blastcaster.php', // Main project file.
          potComments: '', // The copyright at the beginning of the POT file.
          potHeaders: {
            poedit: true, // Includes common Poedit headers.
            'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
          }, // Headers to add to the generated POT file.
          type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
          updateTimestamp: true, // Whether the POT-Creation-Date should be updated without other changes.
          updatePoFiles: true // Whether to update PO files in the same directory as the POT file.
        }
      }
    },

    // Configure the WordPress i18n addtextdomain task
    addtextdomain: {
      options: {
        textdomain: 'blastcaster' // Project text domain.
      },
      target: {
        files: {
          src: [
            '*.php',
            '**/*.php',
            '!node_modules/**'
          ]
        }
      }
    },

    // Configure the grunt-po2mo task for converting .po files to binary .mo files
    po2mo: {
      files: {
        src: 'languages/*.po',
        expand: true
      }
    },

    // Configure the compress task
    compress: {
      main: {
        options: {
          archive: 'build/blastcaster.zip'
        },
        src: [
          'admin/**/*.php',
          'admin/js/*.js',
          'admin/css/*.css',
          'admin/images/**',
          'includes/**',
          'languages/**',
          'blastcaster.php',
          'index.php',
          'readme.txt',
          'uninstall.php'
        ]
      }
    }
  });

  /**
   * Load Grunt plugins and tasks
   */
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpcs');
  grunt.loadNpmTasks("grunt-webpack");
  grunt.loadNpmTasks('grunt-composer');
  grunt.loadNpmTasks('grunt-wp-i18n');
  grunt.loadNpmTasks('grunt-po2mo');

  /**
   * init
   * Build initialization
   */
  grunt.registerTask('init', 'Build initialization', function () {
    if (!grunt.file.isDir('./reports')) {
      grunt.file.mkdir('./reports');
    }
  });

  /**
   * clean
   * Cleans all the generated files
   */
  grunt.registerMultiTask('clean', 'Clean all generated files', function () {
    this.filesSrc.forEach(function (filepath) {
      grunt.log.writeln('Removing ' + filepath + '...');
      grunt.file.delete(filepath);
    });
  });

  /**
   * check
   * Check coverage
   */
  grunt.registerTask('check', 'Check code coverage', function () {
    this.requires('test-all-dist');
    checkCoverage(this.async());
  });

  // Set the default clean to be just 'build'
  multitasker.setDefaultTargets('clean', 'build');

  /**
   * phpmd
   * Run phpmd task using the task configuration to spawn a command with
   * specified arguments.
   */
  grunt.registerMultiTask('phpmd', 'Runs PHP Mess Detector analysis.', runFn(phpmdCompleted));

  /**
   * phpunit
   * Run phpunit task using the task configuration to spawn a command with
   * specified arguments.
   */
  grunt.registerMultiTask('phpunit', 'Runs PHPUnit tests.', runFn(phpUnitCompleted));

  /**
   * phpunit-cov
   * Run phpunit task using the task configuration to spawn a command with
   * specified arguments with no coverage
   */
  grunt.registerMultiTask('phpunit-cov', 'Runs PHPUnit tests.', runPhpWithXdebug(phpUnitCompleted));

  // Test task - run phpcs and phpunit
  grunt.registerTask('test', function () {
    if (0 < this.args.length) {
      grunt.task.run('phpunit:application:tests/' + this.args[0]);
    } else {
      grunt.task.run('test-all');
    }
  });


  // Test task - run phpcs and phpunit
  grunt.registerTask('test-all', ['init', 'phpcs', 'phpmd', 'phpunit']);

  // Test DIST task - run phpcs and phpunit
  grunt.registerTask('test-all-dist', ['init', 'phpcs', 'phpmd', 'phpunit-cov']);

  // Build task aliases
  grunt.registerTask('build-dist', ['test-all-dist', 'check', 'webpack:build', 'makepot', 'po2mo', 'compress']);

  // Alias the default task to build
  grunt.registerTask('default', ['build-dist']);
};
