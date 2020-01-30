<?php
declare (strict_types = 1);

// each client should remember their session id for EXACTLY 1 hour
ini_set('session.gc_maxlifetime', '3600');
ini_set('session.gc_divisor', '1');
ini_set('session.gc_probability', '1');
ini_set('session.cookie_lifetime', '0');
session_set_cookie_params(3600);
// ini_set('session.save_path', '../tmp');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
	ini_set('session.cookie_secure', '1'); // only for https
}
// start application session
session_start();

date_default_timezone_set("Europe/Berlin");
// only for dev
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Classes;
use App\Routing;
// FastRoute determines if a request is valid and
// can actually be handled by the application
use Middlewares\FastRoute;
// sends Request to the handler configured for
// that route in the routes definition
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
// use Zend\Diactoros\Response;
// request handler or dispatcher
use Relay\Relay;
// pull together all the information necessary to
// create a new Request and hand it off to Relay
use Zend\Diactoros\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';
// DI container
$container = Classes::registerInContainer();
// getting all routes
$route = new Routing();
$routes = $route->routes();
// putting routes into middleware
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

// dispatch the request
$requestHandler = new Relay($middlewareQueue);
// response from handler
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());
// emitting the response
$emitter = new SapiEmitter();

return $emitter->emit($response);
