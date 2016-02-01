<?php
namespace air\app\core;

/**
 * Check for value change, alert if quality evaluation changed
 */
class Watcher
{
    /** check interval in seconds */
    const CHECK_INTERVAL = 10;
    /** @var DIContainer */
    protected $resolver;
    /** @var array List of chat ID's */
    protected $subscribers = [];

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function watch()
    {
        $this->resolver->storage->getCo2PpmLast()->then(function($co2ppm) {
            if (null === $co2ppm) {
                return;
            }

            foreach ($this->subscribers as $chatId) {
                $this->resolver->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => (new Formatter)->formatCo2Ppm($co2ppm),
                ]);
            }
        });
    }

    /**
     * Add chat to notified list
     * @param string $chatId
     */
    public function subscribe($chatId)
    {
        $this->subscribers[] = $chatId;
    }

}