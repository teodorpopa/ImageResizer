<?php

namespace TeodorPopa\ImageResizer\Processors;

use TeodorPopa\ImageResizer\Exceptions\ProcessorException;
use TeodorPopa\ImageResizer\ImageResizer;
use TeodorPopa\ImageResizer\Images\ImageFactory;

abstract class AbstractProcessor
{
    /**
     * @var string
     */
    protected $resizeType = ImageResizer::RESIZE_TYPE_AUTO;

    /**
     * @var array|string
     */
    protected $background = '#ffffff';

    /**
     * @var string
     */
    protected $resizePositionX = ImageResizer::RESIZE_POSITION_CENTER;

    /**
     * @var string
     */
    protected $resizePositionY = ImageResizer::RESIZE_POSITION_MIDDLE;

    /**
     * @var resource
     */
    protected $loadedImage;

    /**
     * @var resource
     */
    protected $processedImage;

    /**
     * @param string $filename
     * @throws ProcessorException
     */
    public function __construct($filename = null)
    {
        if (!file_exists($filename)) {
            throw new ProcessorException('The specified image does not exist.');
        }

        $this->loadImage($filename);
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options = array())
    {
        if (array_key_exists('resizeType', $options)) {
            $this->resizeType = $options['resizeType'];
        }

        if (array_key_exists('background', $options)) {
            $this->background = $options['background'];
        }

        if (array_key_exists('resizePositionX', $options)) {
            $this->resizePositionX = $options['resizePositionX'];
        }

        if (array_key_exists('resizePositionY', $options)) {
            $this->resizePositionY = $options['resizePositionY'];
        }
    }

    /**
     * @param string $filename
     * @return $this
     * @throws ProcessorException
     */
    protected function loadImage($filename = null)
    {
        $resource = null;

        if (empty($filename)) {
            $resource = ImageFactory::factory(null, ['width' => 1, 'height' => 1], 'image');
        } else {
            $imageType = $this->getImageMimeType($filename);

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $resource = ImageFactory::factory($filename, [], 'jpeg');
                    break;
                case IMAGETYPE_PNG:
                    $resource = ImageFactory::factory($filename, [], 'png');
                    break;
                case IMAGETYPE_GIF:
                    $resource = ImageFactory::factory($filename, [], 'gif');
                    break;
                default:
                    throw new ProcessorException('This image type can\'t be resized right now.');
            }
        }

        if (empty($resource)) {
            throw new ProcessorException('There was a problem loading the image');
        }

        $this->loadedImage = $resource;

        return $this;
    }

    /**
     * Calculate the image ratio
     *
     * @param int $width
     * @param int $height
     * @return string
     * @throws ProcessorException
     */
    protected function getRatio($width = null, $height = null)
    {
        if (empty($width) || empty($height)) {
            throw new ProcessorException('Cannot calculate the image ratio. Please provide width and height.');
        }

        return number_format(((int)$width / (int)$height), 2, '.', '');
    }


}