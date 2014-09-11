module.exports = function(grunt) {
    grunt.initConfig({
        concat: {
            options: {
                separator: "\r\n",
                stripBanners: true
            },
            dist: {
                src: [
                    'StaticObservable.php',
                    'Configure/Configure.php',
                    'Configure/exception.php',
                    'Debug/Debug.php',
                    'Debug/baseRenderer.php',
                    'Debug/ErrorRenderer.php',
                    'Debug/ExceptionRenderer.php',
                    'DB.php',
                    'DBSimple.php',
                    'UParser.php',
                    'Model.php',
                    'Exception.php',
                    'Validator.php',
                    'Validators/Exception.php',
                    'Validators/FileInSecuredFolder.php',
                    'View/View.php',
                    'View/Exception.php',
                    'Controller/Controller.php',
                    'Response/Response.php',
                    'Response/HttpResponse.php',
                    'Response/JsonResponse.php',
                    'Dispatcher/Dispatcher.php',
                    'Dispatcher/Route.php',
                    'Dispatcher/HttpRoute.php',
                    'Dispatcher/RouteException.php',
                    'Request/Request.php',
                    'Request/HttpRequest.php',
                    'Request/ValidationException.php',
                    'Request/Validator/Validator.php',
                    'Request/Validator/Email.php',
                    'Request/Validator/Integer.php',
                    'Request/Validator/Url.php',
                    'SimpleCache.php',
                    'Cache/Exception.php',
                    'Cache/Engine/CacheEngineInterface.php',
                    'Cache/Engine/FileCache.php',
                    'Cache/Engine/Memcache.php'
                ],
                dest: 'release/0.5/faid-0.5.php'
            }
        },
        phpunit: {
            api: {
                dir: 'tests/configure/'
            },
            controller: {
                dir: 'tests/Controller/'
            },
            dispatcher: {
                dir: 'tests/Dispatcher/'
            },
            staticobservable: {
                dir: 'tests/StaticObservable/'
            },
            view: {
                dir: 'tests/View/'
            },
            configure : {
                dir : 'tests/configure/'
            },
            cache : {
                dir : 'tests/Cache/'
            },
            validators : {
                dir : 'tests/Validators/'
            },
            options: {
                bin : '/Users/gisma/.composer/vendor/bin/phpunit',
                bootstrap: 'tests/loader.php',
                colors: true
            }
        }

    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-phpunit');

    grunt.registerTask('default', ['concat']);
};