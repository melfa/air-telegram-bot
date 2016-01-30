<?php
namespace air\app;

use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\Response;
use React\Filesystem\Filesystem;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\RouteCollector;
use Telegram\Bot\Api;
use air\app\telegram\AirCommand;
use air\app\telegram\StartCommand;

require(__DIR__ . '/../vendor/autoload.php');

class App
{
    const LISTEN_HOST = '0.0.0.0';
    const LISTEN_PORT = 18062;

    /** @var \React\Socket\Server */
    protected $socket;
    /** @var \React\EventLoop\LoopInterface */
    protected $loop;
    /** @var Dispatcher */
    protected $dispatcher;
    /** @var DIContainer */
    protected $resolver;

    public function __construct()
    {
        $this->loop = Factory::create();
        $this->socket = new \React\Socket\Server($this->loop);
        $this->filesystem = Filesystem::create(Factory::create());

        $config = $this->getConfig();
        $runtimePath = __DIR__ . '/' . $config->runtimePath;

        $this->resolver = new DIContainer;
        $this->resolver->config = $config;
        $this->resolver->storage = new Storage($this->filesystem, $runtimePath . '/storage.json');
        $this->resolver->telegram = new Api($config->telegram->apiToken, true);
        $this->resolver->telegram->addCommand(new StartCommand($this->resolver->storage));
        $this->resolver->telegram->addCommand(new AirCommand($this->resolver->storage));

        $this->dispatcher = new Dispatcher($this->routes()->getData(), $this->resolver);

        $http = new \React\Http\Server($this->socket);

        $http->on('request', function (Request $request, Response $response) {
            $this->onRequest($request, $response);
        });
    }

    protected function routes()
    {
        return (new RouteCollector)
            ->controller('/air', 'air\app\AirController');
    }

    protected function getConfig()
    {
        $configFileName = file_exists(__DIR__ . '/../config.local.json') ?
            __DIR__ . '/../config.local.json' : __DIR__ . '/../config.json';
        return json_decode(file_get_contents($configFileName));
    }

    public function run()
    {
        $this->socket->listen(self::LISTEN_PORT, self::LISTEN_HOST);
        $this->loop->run();
    }

    protected function onRequest(Request $request, Response $response)
    {
        $this->resolver->request = $request;
        $this->resolver->response = $response;

        $responseStatus = 200;
        $responseData = null;
        try {
            $responseData = $this->dispatcher->dispatch($request->getMethod(), $request->getPath());
        } catch (HttpRouteNotFoundException $e) {
            $responseStatus = 404;
            $responseData = 'Page not found. Try harder';
        } catch (HttpMethodNotAllowedException $e) {
            $responseStatus = 405;
            $responseData = 'Method not allowed. Use force';
        }

        $response->writeHead($responseStatus);
        $response->end($responseData);
    }

}

(new App)->run();
