module.exports = function(grunt) {
    grunt.initConfig({
        
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
            response : {
                dir : 'tests/Response/'
            },
            validators : {
                dir : 'tests/Validators/'
            },
            requests : {
                dir : 'tests/Request'
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