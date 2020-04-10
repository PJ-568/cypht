<?php

/* all the things */
error_reporting(E_ALL | E_STRICT);

/* debug mode has to be set to something or include files will die() */
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

/* determine current absolute path used for require statements */
define('APP_PATH', dirname(dirname(dirname(__FILE__))).'/');
define('VENDOR_PATH', APP_PATH.'vendor/');
define('WEB_ROOT', '');

/* random id */
define('SITE_ID', 'randomid');

/* cache id */
define('CACHE_ID', 'asdf');

/* get mock objects */
require APP_PATH.'tests/phpunit/mocks.php';

/* get the framework */
require APP_PATH.'lib/framework.php';

/* get the stubs */
require APP_PATH.'tests/phpunit/stubs.php';

?>
