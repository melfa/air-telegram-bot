<?php
namespace air\app;

use Phroute\Phroute\RouteCollector;

$router = new RouteCollector;

$router->get('/setup', ['air\app\actions\Setup', 'run']);
$router->get('/release', ['air\app\actions\Release', 'run']);
$router->post('/telegraf', ['air\app\actions\Telegram', 'run']);
$router->get('/air', ['air\app\actions\Air', 'run']);

return $router;
