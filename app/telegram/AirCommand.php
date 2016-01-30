<?php
namespace air\app\telegram;

use air\app\Storage;
use Telegram\Bot\Commands\Command;

class AirCommand extends Command
{
    /** @var string Command Name */
    protected $name = 'air';
    /** @var string Command Description */
    protected $description = 'Show CO2 ppm in office';
    /** @var Storage */
    protected $storage;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    public function handle($arguments)
    {
        $co2ppm = $this->storage->getCo2Ppm();
        $this->replyWithMessage(['text' => $co2ppm . ' ppm']);
    }

}
