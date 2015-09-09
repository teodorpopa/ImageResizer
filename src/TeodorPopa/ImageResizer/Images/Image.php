<?php

namespace TeodorPopa\ImageResizer\Images;

class Image extends BaseImage implements ImageInterface
{
    /**
     * @param string $filename
     * @param array $options
     * @return BaseImage
     */
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

        $this->imageResource = $resource;

        return $this;
    }

}