<?php
namespace air\app\telegram;

use air\app\core\Watcher;
use Telegram\Bot\Commands\Command;

class WatchCommand extends Command
{
    /** @var string Command Name */
    protected $name = 'watch';
    /** @var string Command Description */
    protected $description = 'Subscribe to alerts on air evaluation changed';
    /** @var Watcher */
    protected $watcher;

    public function __construct($watcher)
    {
        $this->watcher = $watcher;
    }


    public function handle($arguments)
    {
        $this->watcher->subscribe($this->getUpdate()->getMessage()->getChat()->getId());
    }

}
