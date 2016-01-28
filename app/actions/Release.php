<?php
namespace air\app\actions;

use Telegram\Bot\Api;
use air\app\telegram\AirCommand;
use air\app\telegram\StartCommand;

class Release extends Action
{

    public function run()
    {
        $telegram = new Api('149083827:AAFPrapXL65wWplM6NJKda36wcBid9ivZho', true);
        $telegram->removeWebhook();
    }

}
