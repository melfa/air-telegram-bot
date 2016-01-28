<?php
namespace air\app;

use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\Response;
use Phroute\Phroute\Dispatcher;

require('../vendor/autoload.php');
$router = require('routes.php');
$dispatcher = new Dispatcher($router->getData());

$app = function (Request $request, Response $response) use ($dispatcher) {
    try {
        $actionResult = $dispatcher->dispatch($request->getMethod(), $request->getPath());
        $response->writeHead(200);
        $response->end($actionResult);
    }
    catch (HttpRouteNotFoundException $e) {
        $response->writeHead(404);
        $response->end('Page not found. Try harder');
    }
    catch (HttpMethodNotAllowedException $e) {
        $response->writeHead(405);
        $response->end('Method not allowed. Use force');
    }
};

$loop = Factory::create();
$socket = new \React\Socket\Server($loop);
$http = new \React\Http\Server($socket);

$http->on('request', $app);

$socket->listen(18062, '0.0.0.0');
$loop->run();
