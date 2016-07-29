# uglify
[![Latest Stable Version](https://poser.pugx.org/nodejs-php-fallback/uglify/v/stable.png)](https://packagist.org/packages/nodejs-php-fallback/uglify)
[![Build Status](https://travis-ci.org/kylekatarnls/uglify.svg?branch=master)](https://travis-ci.org/kylekatarnls/uglify)
[![StyleCI](https://styleci.io/repos/64242033/shield?style=flat)](https://styleci.io/repos/64242033)
[![Test Coverage](https://codeclimate.com/github/kylekatarnls/uglify/badges/coverage.svg)](https://codecov.io/github/kylekatarnls/uglify?branch=master)
[![Code Climate](https://codeclimate.com/github/kylekatarnls/uglify/badges/gpa.svg)](https://codeclimate.com/github/kylekatarnls/uglify)

Simple PHP class to minify both your javascript and css the best existing way (uglify-js for JS, clean-css for CSS) and if node is not available, PHP fallbacks are used instead.

## Usage

First you need [composer](https://getcomposer.org/) if you have not already. Then get the package with ```composer require nodejs-php-fallback/uglify``` then require the composer autload in your PHP file if it's not already:
```php
<?php

use NodejsPhpFallback\Uglify;

// Require the composer autoload in your PHP file if it's not already.
// You do not need to if you use a framework with composer like Symfony, Laravel, etc.
require 'vendor/autoload.php';

$uglify = new Uglify(array(
    'path/to/my-first-file.js',
    'path/to/my-second-file.js',
));
$uglify->add('path/to/my-thrid-file.js');

// Output to a file:
$uglify->write('path/to/destination.min.js');

// Output to the browser:
header('Content-type: text/javascript');
echo $uglify;
```

**Uglify** will use js minification by default. If the first source path end with *.css* or you use ```->write()``` with a path ending with *.css*, it will switch to CSS mode. Else you can switch manually or get explicitly JS/CSS minified:

```php
$uglify->jsMode();
echo $uglify; // display minified javascript
$uglify->cssMode();
echo $uglify; // display minified css

// or
echo $uglify->getMinifiedJs(); // display minified javascript
echo $uglify->getMinifiedCss(); // display minified css
```
