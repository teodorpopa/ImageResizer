<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

abstract class BaseImage
{
    /**
     * @var resource
     */
    protected $imageResource;

    public function __construct($filename = null, array $options = array())
    {
        if (!file_exists($filename)) {
            throw new SetupException('The specified image does not exist.');
        }
    }

    /**
     * Returns the loaded image resource
     *
     * @return resource
     */
    public function getResource()
    {
        if(empty($this->imageResource)) {
            throw new SetupException('There is no image resource available.');
        }

        return $this->imageResource;
    }

}