<?php
namespace air\app\telegram;

use Yii;
use Telegram\Bot\Commands\Command;

class AirCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'air';

    /**
     * @var string Command Description
     */
    protected $description = 'Show CO2 ppm in office';

    public function handle($arguments)
    {
        $co2ppm = 1;
        $this->replyWithMessage(['text' => $co2ppm . ' ppm']);
    }

}
