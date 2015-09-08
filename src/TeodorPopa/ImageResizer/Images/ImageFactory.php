<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\ProcessorException;

class ImageFactory
{
    public static function factory($filename = null, array $options = array(), $imageType = 'jpeg')
    {
        switch($imageType) {
            case 'jpeg':
                return new Jpeg($filename, $options);
            case 'png':
                return new Png($filename, $options);
            case 'gif':
                return new Gif($filename, $options);
            case 'image':
                return new Image(null, $options);
            default:
                throw new ProcessorException('There was an error trying to create the image resource.');
        }
    }

}