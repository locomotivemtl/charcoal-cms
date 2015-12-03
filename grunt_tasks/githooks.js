/**
* @file Bind Grunt tasks to Git hooks
*/

module.exports = {
    all: {
        'pre-commit': 'phplint phpunit phpcs'
    }
};
