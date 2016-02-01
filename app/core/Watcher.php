<?php
namespace air\app\core;

/**
 * Check for value change, alert if quality evaluation changed
 */
class Watcher
{
    const STATE_NAP = 0;
    const STATE_READY = 1;

    /** check interval in seconds */
    const CHECK_INTERVAL = 10;

    /** @var DIContainer */
    protected $resolver;

    /** @var array List of chat ID's */
    protected $subscribers = [];

    /** @var \stdClass */
    protected $co2prev;

    /** @var int */
    protected $state = self::STATE_NAP;

    /** @var int Spam prevention */
    protected $notifyCount = 0;


    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Check CO2 value, compare to previous and call notify if limit reached
     */
    public function watch()
    {
        if (!$this->subscribers) {
            return;
        }

        // todo check night time

        $this->resolver->storage->getCo2Ppm()->then(function($co2value) {
            if (null === $co2value) {
                return;
            }

            if (null === $this->co2prev) {
                $this->co2prev = $co2value;
                return;
            }

            echo "state {$this->state} current {$co2value->ppm}:{$co2value->time} prev {$this->co2prev->ppm}:{$this->co2prev->time}" . PHP_EOL;

            switch ($this->state) {
                case self::STATE_NAP:
                    if ($this->getIndex($co2value->ppm) != $this->getIndex($this->co2prev->ppm)) {
                        $this->state = self::STATE_READY;
                    } else {
                        $this->co2prev = $co2value;
                    }
                    break;

                case self::STATE_READY:
                    if ($this->getIndex($co2value->ppm) == $this->getIndex($this->co2prev->ppm)) {
                        $this->state = self::STATE_NAP;
                        $this->co2prev = $co2value;
                    } elseif (abs($co2value->ppm - $this->co2prev->ppm) > 50) {
                        // alert if value diff more then 50
                        $this->notify($co2value->ppm);
                        $this->co2prev = $co2value;
                    } elseif ($co2value->time - $this->co2prev->time > 600) {
                        // alert if limit reached 10 minutes ago
                        $this->notify($co2value->ppm);
                        $this->co2prev = $co2value;
                    }
                    break;
            }

        });
    }

    /**
     * Add chat to notified list
     * @param string $chatId
     */
    public function subscribe($chatId)
    {
        if (false !== array_search($chatId, $this->subscribers)) {
            return;
        }

        $this->subscribers[] = $chatId;
        echo "$chatId subscribed" . PHP_EOL;
    }

    /**
     * Remove chat from notified list
     * @param string $chatId
     */
    public function unsubscribe($chatId)
    {
        $this->subscribers = array_diff($this->subscribers, [$chatId]);
        echo "$chatId unsubscribed" . PHP_EOL;
    }

    protected function getIndex($co2ppm)
    {
        foreach ([Formatter::FRESH, Formatter::SO_SO, Formatter::NOT_GOOD, Formatter::DANGER] as $index => $limit) {
            if ($co2ppm < $limit) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Notify subscribers chats
     * @param int $co2ppm
     */
    protected function notify($co2ppm)
    {
        if ($this->notifyCount > 50) {
            // spam prevention
            echo "Spam prevented" . PHP_EOL;
            return;
        }

        echo "Notify" . PHP_EOL;

        foreach ($this->subscribers as $chatId) {
            $this->resolver->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => (new Formatter)->formatCo2Ppm($co2ppm),
            ]);
            $this->notifyCount++;
        }
    }

}
