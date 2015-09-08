<?php

namespace TeodorPopa\ImageResizer\Images;

interface ImageInterface
{

    public function __construct($filename = null, $options = array());

    public function output($image);

    public function save($resource = null, $filename = null, $quality = 10);

}