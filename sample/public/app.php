<?php

use Symfony\Component\HttpFoundation\Request;

// Trust the HTTP_X_FORWARDED_PROTO var to enable HTTPS
if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == 'https') {
    $_SERVER["HTTPS"] = 'yes';
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../../vendor/autoload.php';

\Symfony\Component\Debug\Debug::enable();

$kernel = new \Sample\Kernel('prod', true);

// $kernel = new AppCache($kernel);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
// Request::enableHttpMethodParameterOverride();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);