<?php

namespace TeodorPopa\ImageResizer\Processors;

interface Processor
{

    public function __construct($filename = null);

    public function resize($width = null, $height = null, array $options = array());

    public function output();

    public function save($filename = null, $quality = 10, $fileType = IMAGETYPE_JPEG);

    /**
     * @param string $filename
     * @return array|null
     */
    public function getImageMimeType($filename = null);

    /**
     * @param resource $imageResource
     * @return int
     */
    public function getImageWidth($imageResource);

    /**
     * @param resource $imageResource
     * @return int
     */
    public function getImageHeight($imageResource);

}