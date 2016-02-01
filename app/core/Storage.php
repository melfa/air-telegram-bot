<?php
namespace air\app\core;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

class Storage
{
    /** @var Client */
    protected $http;
    /** @var \stdClass */
    protected $influxConfig;

    public function __construct(LoopInterface $loop, $influxConfig)
    {
        $this->influxConfig = $influxConfig;

        $this->http = new Client([
            'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
            'base_uri' => "http://{$influxConfig->host}:{$influxConfig->port}/"
        ]);
    }

    /**
     * Get current CO2 value
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getCo2Ppm()
    {
        return $this->http->getAsync('query', [
            RequestOptions::AUTH => [$this->influxConfig->user, $this->influxConfig->password],
            RequestOptions::QUERY => [
                'db' => 'air',
                'q' => 'select last(ppm) from co2',
                'epoch' => 's', // timestamps in seconds
            ],
        ])->then(
            function (ResponseInterface $response) {
                $influxResponse = json_decode($response->getBody(), true);

                /*
                    {
                        "results": [
                            {
                                "series": [
                                    {
                                        "name": "cpu_load_short",
                                        "columns": [
                                            "time",
                                            "ppm"
                                        ],
                                        "values": [
                                            [
                                                "2015-01-29T21:55:43.702900257Z",
                                                0.55
                                            ],
                                            [
                                                "2015-01-29T21:55:43.702900257Z",
                                                23422
                                            ],
                                            [
                                                "2015-06-11T20:46:02Z",
                                                0.64
                                            ]
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                 */

                if (!$influxResponse || !is_array($influxResponse)) {
                    return null;
                }

                $value = $influxResponse['results'][0]['series'][0]['values'][0];
                return (object) [
                    'time' => $value[0],
                    'ppm' => $value[1],
                ];
            },
            function (\Exception $e) {
                // todo logging
                var_dump($e->getMessage());
//                echo $e->getMessage() . "\n";
//                echo $e->getRequest()->getMethod();
            }
        );
    }

}
