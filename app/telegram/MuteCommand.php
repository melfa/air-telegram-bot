<?php
namespace air\app\telegram;

use air\app\core\Watcher;
use Telegram\Bot\Commands\Command;

class MuteCommand extends Command
{
    /** @var string Command Name */
    protected $name = 'mute';
    /** @var string Command Description */
    protected $description = 'Mute alerts';
    /** @var Watcher */
    protected $watcher;

    public function __construct($watcher)
    {
        $this->watcher = $watcher;
    }


    public function handle($arguments)
    {
        $this->watcher->unsubscribe($this->getUpdate()->getMessage()->getChat()->getId());
    }

}
