<?php
namespace air\app\core;


class Formatter
{
    const FRESH = 600;
    const SO_SO = 1000;
    const NOT_GOOD = 2500;
    const DANGER = 5000;

    /**
     * @param int $co2ppm
     * @return string
     */
    public function formatCo2Ppm($co2ppm)
    {
        $evaluation = '';
        if ($co2ppm < self::FRESH) {
            $evaluation = 'fresh';
        } elseif ($co2ppm < self::SO_SO) {
            $evaluation = 'so-so';
        } elseif ($co2ppm < self::NOT_GOOD) {
            $evaluation = 'not good';
        } elseif ($co2ppm < self::DANGER) {
            $evaluation = 'danger';
        }
        $evaluation = ucfirst($evaluation);
        return "$evaluation ($co2ppm ppm)";
    }

}
