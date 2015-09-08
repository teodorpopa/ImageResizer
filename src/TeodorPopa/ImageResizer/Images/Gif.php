<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

class Gif implements ImageInterface
{

    public function __construct($filename = null, $options = array())
    {
        if (!file_exists($filename)) {
            throw new SetupException('The specified image does not exist.');
        }

        $resource = imagecreatefromgif($filename);

        return $resource;
    }

    public function output($image)
    {
        header("Content-type: image/gif");

        imagegif($image);
    }

    public function save($resource = null, $filename = null, $quality = 10)
    {
        imagegif($resource, $filename);

        return true;
    }
}