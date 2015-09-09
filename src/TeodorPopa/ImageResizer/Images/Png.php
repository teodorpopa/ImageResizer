<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

class Png extends BaseImage implements ImageInterface
{
    /**
     * @param string $filename
     * @param array $options
     */
    public function __construct($filename = null, $options = array())
    {
        $resource = imagecreatefrompng($filename);

        imagealphablending($resource, false);
        imagesavealpha($resource, true);

        $this->imageResource = $resource;
    }

}