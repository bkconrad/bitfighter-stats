task('build', function () {
    var compressedJsResult;
    var combinedJsResult;
    var fs = require('fs');
    var path = require('path');
    var UglifyJS = require('uglify-js');
    var UglifyCSS = require('uglifycss');

    var jsFilesToCombine = [
          './lib/moment.min.js'
        , './lib/ng-grid/lib/jquery-1.9.1.js'
        , './lib/angular.min.js'
        , './lib/angular-route.min.js'
        , './lib/loading-bar.min.js'
        // , './lib/ng-grid/lib/jquery-ui-1.9.1.custom.min.js'
        // , './lib/ng-grid/lib/jquery.layout-latest.min.js'
        , './lib/ng-grid/ng-grid-2.0.7.min.js'
        , './lib/bootstrap/js/bootstrap.min.js'
        , './lib/d3/d3.v3.min.js'
        , './lib/es5-shim.min.js'
    ];

    var jsFilesToCompress = [
          './app/js/app.js'
        , './app/js/controllers.js'
        , './app/js/filters.js'
        , './app/js/directives.js'
    ];

    var cssFilesToCompress = [
        'lib/ng-grid/ng-grid.min.css'
      , 'lib/loading-bar.min.css'
      // , 'lib/ng-grid/lib/bootstrap/css/bootstrap.min.css'
      , 'lib/ng-grid/lib/bootstrap/css/bootstrap-responsive.min.css'
      , 'lib/bootstrap/css/bootstrap.min.css'
      , 'app/css/style.css'
    ];

    if(!fs.existsSync('build')) {
        fs.mkdirSync('build');
    }

    // write the browserified and minified source
    filename = path.join('build', 'bfstats.min.js');

    console.log('Combining JS...');
    combinedJsResult = UglifyJS.minify(jsFilesToCombine, {
        compress: false
    });

    console.log('Compressing JS...');
    compressedJsResult = UglifyJS.minify(jsFilesToCompress);

    console.log('Writing...')
    fs.writeFileSync(filename, combinedJsResult.code + compressedJsResult.code);

    console.log('Wrote to ' + filename + '. Size: ' + (compressedJsResult.code.length + combinedJsResult.code.length));

    // write the browserified and minified source
    filename = path.join('build', 'bfstats.min.css');

    console.log('Compressing CSS...');
    compressedCssResult = UglifyCSS.processFiles(cssFilesToCompress);

    console.log('Writing...')
    fs.writeFileSync(filename, compressedCssResult);

    console.log('Wrote to ' + filename + '. Size: ' + compressedCssResult.length);
});

task('lint', function () {
    var beautify = require('js-beautify').js_beautify;
    var JSHINT = require('jshint').JSHINT;
    var JSLINT = require('jslint').load();
    var fs = require('fs');
    var path = require('path');

    var JSBEAUTIFY_OPTS = {
        jslint_happy: true,
        good_stuff: true,
        indent_size: 4
    };

    var JSHINT_OPTS = {
    };

    var JSLINT_OPTS = {
        bitwise: true,
        nomen: true,
        sloppy: true,
        vars: true,
        plusplus: true,
        node: true,
        white: true,
        unparam: true,
        predef: ['moment', 'angular', 'd3', 'it', 'describe', 'beforeEach']
    };

    var srcFiles = fs.readdirSync('app/js').map(function(x) { return path.join('app', 'js', x); });
    var files = srcFiles;
    var totalErrors = 0;

    files.forEach(function(name) {
        process.stdout.write("Linting " + name + "\n");
        var data = fs.readFileSync(name);
        data = beautify(data.toString(), JSBEAUTIFY_OPTS)
        fs.writeFileSync(name, data);

        if(!JSHINT(data, JSHINT_OPTS)) {
            process.stdout.write("jshint:\n");
            var hintData = JSHINT.data();
            for(var i = 0; i < hintData.errors.length; i++) {
                var error = hintData.errors[i];
                if(error) {
                    process.stdout.write(name + ":" + error.line + ":" + error.character + " " + error.reason + "\n");
                    totalErrors++;
                }
            }
        }

        if(!JSLINT(data, JSLINT_OPTS)) {
            process.stdout.write("jslint:\n");
            for(var i = 0; i < JSLINT.errors.length; i++) {
                var error = JSLINT.errors[i];
                if(error) {
                    process.stdout.write(name + ":" + error.line + ":" + error.character + " " + error.reason + "\n");
                    totalErrors++;
                }
            }
        }
    });

    if(totalErrors > 0) {
        process.stdout.write(totalErrors + " errors detected!\n");
        process.exit(1);
    } else {
        process.stdout.write("Lint-free!\n");
    }
});
