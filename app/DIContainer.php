<?php
namespace air\app;

use Phroute\Phroute\HandlerResolverInterface;
use React\Http\Request;
use React\Http\Response;
use Telegram\Bot\Api;

class DIContainer implements HandlerResolverInterface
{
    /** @var \stdClass */
    public $config;
    /** @var Request */
    public $request;
    /** @var Response */
    public $response;
    /** @var Storage */
    public $storage;
    /** @var Api */
    public $telegram;

    public function resolve($handler)
    {
        /*
         * Only attempt resolve uninstantiated objects which will be in the form:
         *
         *      $handler = ['App\Controllers\Home', 'method'];
         */
        if (is_array($handler) && is_string($handler[0]) && 'air\app\AirController' == $handler[0]) {
            $handler[0] = new AirController($this);
        }

        return $handler;
    }

}
