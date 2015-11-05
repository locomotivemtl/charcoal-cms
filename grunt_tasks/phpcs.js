/**
* @file Analyze PHP files for coding standard violations
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
        extensions: 'php',
        showSniffCodes: true
    }
};
