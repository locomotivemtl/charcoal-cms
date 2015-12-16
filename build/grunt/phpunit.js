/**
* @file PHPUnit Tester
*
* Executes unit tests for PHP.
*/

module.exports = {
    src: {
        dir: 'tests/'
    },
    options: {
        bin: 'vendor/bin/phpunit',
        configuration: 'tests/phpunit.xml'
    }
};
