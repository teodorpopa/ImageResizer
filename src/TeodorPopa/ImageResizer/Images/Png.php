<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

class Png implements ImageInterface
{

    public function __construct($filename = null, $options = array())
    {
        if (!file_exists($filename)) {
            throw new SetupException('The specified image does not exist.');
        }

        $resource = imagecreatefrompng($filename);

        imagealphablending($resource, false);
        imagesavealpha($resource, true);

        return $resource;
    }

    public function output($image)
    {
        header("Content-type: image/png");

        imagepng($image);
    }

    public function save($resource = null, $filename = null, $quality = 10)
    {
        $quality--;

        imagepng($resource, $filename, $quality);

        return true;
    }
}