<?php
namespace air\app\actions;

use Telegram\Bot\Api;
use air\app\telegram\AirCommand;
use air\app\telegram\StartCommand;
use Telegram\Bot\Objects\Update;

class Telegram extends Action
{

    public function run()
    {
        $telegram = new Api('149083827:AAFPrapXL65wWplM6NJKda36wcBid9ivZho', true);
        $telegram->addCommand(StartCommand::class);
        $telegram->addCommand(AirCommand::class);

        $body = ''; // todo pipe https://github.com/reactphp/http/issues/7
        $update = new Update(json_decode($body));
        $message = $update->getMessage();

        if ($message !== null && $message->has('text')) {
            $telegram->getCommandBus()->handler($message->getText(), $update);
        }
    }

}
