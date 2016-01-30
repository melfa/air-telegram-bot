<?php
namespace air\app;

use React\Stream\BufferedSink;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class AirController
{
    /** @var DIContainer */
    protected $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function getSet($ppm)
    {
        $this->resolver->storage->setCo2Ppm($ppm);
    }

    public function getSetup()
    {
        $host = $this->resolver->config->telegram->webhookHost;
        $this->resolver->telegram->setWebhook(['url' => "https://{$host}/air/telegram"]);
    }

    public function getRelease()
    {
        $this->resolver->telegram->removeWebhook();
    }

    public function postTelegram()
    {
        BufferedSink::createPromise($this->resolver->request)->then(function($body) {
            $update = new Update(json_decode($body, true));
            $message = $update->getMessage();

            if ($message !== null && $message->has('text')) {
                $this->resolver->telegram->getCommandBus()->handler($message->getText(), $update);
            }
        });
    }

}
