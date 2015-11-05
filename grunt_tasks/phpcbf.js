/**
* @file Automatically correct coding standard violations in PHP, CSS, and JS files
*/

module.exports = {
    src: {
        src: ['src/**/*.php']
    },
    tests: {
        src: ['tests/**/*.php']
    },
    options: {
        standard: 'phpcs.xml',
        noPatch: true
    }
};
