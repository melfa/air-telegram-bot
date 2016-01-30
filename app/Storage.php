<?php
namespace air\app;

use React\Filesystem\Filesystem;

class Storage
{
    /** @var Filesystem */
    protected $filesystem;
    /** @var string */
    protected $storageFilePath;
    /** @var array */
    protected $measures;

    public function __construct($filesystem, $storageFilePath)
    {
        $this->filesystem = $filesystem;
        $this->storageFilePath = $storageFilePath;

        $this->filesystem->getContents($this->storageFilePath)->then(function($contents) {
            if (!$contents) {
                return;
            }

            $measures = json_decode($contents, true);
            if (!$measures) {
                $this->measures = [];
                return;
            }

            $this->measures = $measures;
        });
    }

    public function getCo2Ppm()
    {
        if (!$this->measures) {
            return null;
        }

        return $this->measures[count($this->measures) - 1];
    }

    public function setCo2Ppm($ppm)
    {
        $this->measures[] = ['co2ppm' => $ppm, 'time' => time()];
        $this->filesystem->file($this->storageFilePath)->open('cwt')->then(function($stream) {
            $stream->end(json_encode($this->measures));
        });
    }

}
