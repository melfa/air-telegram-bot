<?php
namespace air\app\telegram;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'Welcome screen';

    public function handle($arguments)
    {
        $this->replyWithMessage(['text' => '/air Show CO2 ppm in office']);
    }

}
