<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\ProcessorException;

class ImageFactory
{
    /**
     * Image factory
     *
     * @param null $filename
     * @param array $options
     * @param string $imageType
     * @return resource
     * @throws ProcessorException
     */
    public static function factory($filename = null, array $options = array(), $imageType = 'jpeg')
    {
        switch ($imageType) {
            case 'jpeg':
                $image = new Jpeg($filename, $options);
                break;
            case 'png':
                $image = new Png($filename, $options);
                break;
            case 'gif':
                $image = new Gif($filename, $options);
                break;
            case 'image':
                $image = new Image(null, $options);
                break;
            default:
                throw new ProcessorException('There was an error trying to create the image resource.');
        }

        return $image->getResource();
    }

}