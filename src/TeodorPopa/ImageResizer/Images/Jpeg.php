<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

class Jpeg implements ImageInterface
{

    public function __construct($filename = null, $options = array())
    {
        if (!file_exists($filename)) {
            throw new SetupException('The specified image does not exist.');
        }

        $resource = imagecreatefromjpeg($filename);

        return $resource;
    }

    public function output($image)
    {
        header("Content-type: image/jpeg");

        imagejpeg($image);
    }

    public function save($resource = null, $filename = null, $quality = 10)
    {
        $compression = $quality * 10;

        imagejpeg($resource, $filename, $compression);

        return true;
    }
}