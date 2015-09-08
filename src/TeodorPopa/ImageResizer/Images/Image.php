<?php

namespace TeodorPopa\ImageResizer\Images;

class Image implements ImageInterface
{

    public function __construct($filename = null, $options = array())
    {
        if (!array_key_exists('width', $options) && (int)$options['width'] <= 0) {
            throw new SetupException('Please specify the width of the image.');
        }

        if (!array_key_exists('height', $options) && (int)$options['height'] <= 0) {
            throw new SetupException('Please specify the height of the image.');
        }

        $resource = imagecreatetruecolor($options['width'], $options['height']);

        imagealphablending($resource, false);
        imagesavealpha($resource, true);

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