<?php
namespace air\app;

use Clue\React\Redis\Client;
use Clue\React\Redis\Factory;

class Storage
{
    /** @var array */
    protected $measures = [];
    /** @var Factory */
    protected $redis;
    /** @var string */
    protected $redisConnectString;

    public function __construct(Factory $redis, $redisConnectString)
    {
        $this->redis = $redis;
        $this->redisConnectString = $redisConnectString;

        $redis->createClient($this->redisConnectString)->then(
            function (Client $client) {
                $client->get('measures')->then(function ($measures) {
                    if ($measures) {
                        $this->measures = json_decode($measures, true);
                    }

                });
                $client->end();
            }
        );
    }

    public function getCo2Ppm()
    {
        if (!$this->measures) {
            return null;
        }

        return $this->measures[count($this->measures) - 1]['co2ppm'];
    }

    public function setCo2Ppm($ppm)
    {
        $this->measures[] = ['co2ppm' => $ppm, 'time' => time()];

        $this->redis->createClient($this->redisConnectString)->then(
            function (Client $client) {
                $client->set('measures', json_encode($this->measures));
                $client->end();
            }
        );
    }

}
