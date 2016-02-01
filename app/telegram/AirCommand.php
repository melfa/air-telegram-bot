<?php
namespace air\app\telegram;

use air\app\core\Formatter;
use air\app\core\Storage;
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
        $this->storage->getCo2Ppm()->then(function ($co2value) {
            $this->replyWithMessage([
                'text' => (new Formatter)->formatCo2Ppm($co2value->ppm)
            ]);
        });
    }

}
