<?php

namespace TeodorPopa\ImageResizer\Images;

use TeodorPopa\ImageResizer\Exceptions\SetupException;

abstract class BaseImage
{

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