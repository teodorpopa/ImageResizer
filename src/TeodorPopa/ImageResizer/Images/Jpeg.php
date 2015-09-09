<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

class Jpeg extends BaseImage implements ImageInterface
{
    /**
     * @param string $filename
     * @param array $options
     */
    public function __construct($filename = null, $options = array())
    {
        parent::__construct();

        $resource = imagecreatefromjpeg($filename);

        $this->imageResource = $resource;
    }

}