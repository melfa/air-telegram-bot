<?php
namespace air\app\actions;

use Telegram\Bot\Api;
use air\app\telegram\AirCommand;
use air\app\telegram\StartCommand;

class Release extends Action
{

    public function run()
    {
        $telegram = new Api(TELEGRAM_API_TOKEN, true);
        $telegram->removeWebhook();
    }

}
