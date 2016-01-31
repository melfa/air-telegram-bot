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
        $this->storage->getCo2Ppm()->then(function ($co2ppm) {
            $this->replyWithMessage(['text' => $this->formatCo2Ppm($co2ppm)]);
        });
    }

    /**
     * @param int $co2ppm
     * @return string
     */
    protected function formatCo2Ppm($co2ppm)
    {
        $evaluation = '';
        if ($co2ppm < 600) {
            $evaluation = 'fresh';
        } elseif ($co2ppm < 1000) {
            $evaluation = 'so-so';
        } elseif ($co2ppm < 2500) {
            $evaluation = 'not good';
        } elseif ($co2ppm < 5000) {
            $evaluation = 'danger';
        }
        $evaluation = ucfirst($evaluation);
        return "$evaluation ($co2ppm ppm)";
    }

}
