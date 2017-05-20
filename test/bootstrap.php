<?php

require dirname(__DIR__).'/vendor/autoload.php';

$env = require dirname(__DIR__).'/test/env.php';

define('MAILGUN_KEY', $env['MAILGUN_KEY']);
define('MAILGUN_DOMAIN', $env['MAILGUN_DOMAIN']);
