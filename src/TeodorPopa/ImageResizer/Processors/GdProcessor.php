<?php

namespace TeodorPopa\ImageResizer\Processors;

use TeodorPopa\ImageResizer\ImageResizer;
use TeodorPopa\ImageResizer\Images\ImageFactory;

class GdProcessor extends AbstractProcessor implements Processor
{

    public function resize($width = null, $height = null, array $options = array())
    {
        $this->setOptions($options);

        switch($this->resizeType) {
            case ImageResizer::RESIZE_TYPE_WIDTH:
                $dimensions = $this->getResizeToWidthDimensions($width);
                $this->doResize($width = null, $height = null, $dimensions);
                break;
            case ImageResizer::RESIZE_TYPE_HEIGHT:
                $dimensions = $this->getResizeToHeightDimensions($height);
                $this->doResize($width = null, $height = null, $dimensions);
                break;
            case ImageResizer::RESIZE_TYPE_EXACT:
                $dimensions = $this->getResizeExactDimensions($width, $height);
                $this->doResize($width = null, $height = null, $dimensions);
                break;
            default:
                $this->resizeAuto($width, $height);
        }

        return $this;
    }

    public function output()
    {

    }

    public function save($filename = null, $quality = 10)
    {

    }

    /**
     * Get the dimensions array for resizing to exact width
     *
     * @param int $width
     * @return array
     */
    protected function getResizeToWidthDimensions($width)
    {
        $ratio = $width / $this->getImageWidth($this->loadedImage);
        $height = $this->getImageHeight($this->loadedImage) * $ratio;

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Get the dimensions array for resizing to exact height
     *
     * @param int $height
     * @return array
     */
    protected function getResizeToHeightDimensions($height)
    {
        $ratio = $height / $this->getImageHeight($this->loadedImage);
        $width = $this->getImageWidth($this->loadedImage) * $ratio;

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Return the dimensions array for an exact resize
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    protected function getResizeExactDimensions($width, $height)
    {
        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Resize an image constraining the aspect ratio of the image
     *
     * @param $width
     * @param $height
     * @return resource
     */
    protected function resizeAuto($width, $height)
    {
        $ratio = $this->getRatio($width, $height);
        $originalRatio = $this->getRatio($this->getImageWidth($this->loadedImage), $this->getImageHeight($this->loadedImage));

        switch($ratio) {
            case ($ratio < $originalRatio):
                return $this->resize($width, $height, self::RESIZE_TYPE_WIDTH);
                break;
            case ($ratio > $originalRatio):
                return $this->resize($width, $height, self::RESIZE_TYPE_HEIGHT);
                break;
            default:
                return $this->resize($width, $height, self::RESIZE_TYPE_EXACT);
                break;
        }
    }

    /**
     * @param array $dimensions
     * @return bool
     */
    protected function doResize($width = null, $height = null, $dimensions)
    {
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['height'];

        $left = (($width - $newWidth) != 0) ? (($width - $newWidth) / 2) : 0;
        $top = (($height - $newHeight) != 0) ? (($height - $newHeight) / 2) : 0;

        $newImage = ImageFactory::factory(null, ['width' => $width, 'height' => $height], 'image');

        $background = imagecolorallocate($newImage, 255, 255, 255);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $background);

        imagecopyresampled(
            $newImage,
            $this->loadedImage,
            $left,
            $top,
            0,
            0,
            $width,
            $height,
            $this->getImageWidth($this->loadedImage),
            $this->getImageHeight($this->loadedImage)
        );

        $resizedImage = ImageFactory::factory(null, ['width' => $newWidth, 'height' => $newHeight], 'image');

        imagecopyresampled(
            $resizedImage,
            $newImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $newWidth,
            $newHeight
        );

        $this->processedImage = $resizedImage;

        return true;
    }


    /**
     * @param string $filename
     * @return array|null
     */
    public function getImageMimeType($filename = null)
    {
        if (empty($filename)) {
            return false;
        }

        $imageInfo = getimagesize($filename);

        if(is_array($imageInfo) && isset($imageInfo[2])) {
            return $imageInfo[2];
        }

        return null;
    }

    /**
     * @param resource $imageResource
     * @return int
     */
    public function getImageWidth($imageResource) {
        return imagesx($imageResource);
    }

    /**
     * @param resource $imageResource
     * @return int
     */
    public function getImageHeight($imageResource) {
        return imagesy($imageResource);
    }
}