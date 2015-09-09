<?php

namespace TeodorPopa\ImageResizer\Processors;

use TeodorPopa\ImageResizer\Exceptions\ProcessorException;
use TeodorPopa\ImageResizer\ImageResizer;
use TeodorPopa\ImageResizer\Images\ImageFactory;

class GdProcessor extends AbstractProcessor implements Processor
{

    /**
     * @param int $width
     * @param int $height
     * @param array $options
     * @return $this
     */
    public function resize($width = null, $height = null, array $options = array())
    {
        $this->setOptions($options);

        switch ($this->resizeType) {
            case ImageResizer::RESIZE_TYPE_WIDTH:
                $dimensions = $this->getResizeToWidthDimensions($width);
                $this->doResize($width, $height, $dimensions);
                break;
            case ImageResizer::RESIZE_TYPE_HEIGHT:
                $dimensions = $this->getResizeToHeightDimensions($height);
                $this->doResize($width, $height, $dimensions);
                break;
            case ImageResizer::RESIZE_TYPE_EXACT:
                $dimensions = $this->getResizeExactDimensions($width, $height);
                $this->doResize($width, $height, $dimensions);
                break;
            default:
                $this->resizeAuto($width, $height);
        }

        return $this;
    }

    /**
     * Output an image to a valid IMAGETYPE_XXX type
     * @param int $fileType
     */
    public function output($fileType = IMAGETYPE_JPEG)
    {
        switch($fileType) {
            case IMAGETYPE_JPEG:
                header("Content-type: image/jpeg");
                imagejpeg($this->processedImage);
                break;
            case IMAGETYPE_PNG:
                header("Content-type: image/png");
                imagepng($this->processedImage);
                break;
            case IMAGETYPE_GIF:
                header("Content-type: image/gif");
                imagegif($this->processedImage);
                break;
        }
    }

    /**
     * Saves an image to the specified filename
     *
     * @param null $filename
     * @param int $quality
     * @param int $fileType
     */
    public function save($filename = null, $quality = 10, $fileType = IMAGETYPE_JPEG)
    {
        switch($fileType) {
            case IMAGETYPE_JPEG:
                $compression = $quality * 10;
                imagejpeg($this->processedImage, $filename, $compression);
                break;
            case IMAGETYPE_PNG:
                $quality--;
                imagepng($this->processedImage, $filename, $quality);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->processedImage, $filename);
                break;
        }
    }

    /**
     * Get the dimensions array for resizing to exact width
     *
     * @param int $width
     * @return array
     */
    protected function getResizeToWidthDimensions($width)
    {
        $ratio = $width / $this->getImageSize($this->loadedImage);
        $height = $this->getImageSize($this->loadedImage, 'height') * $ratio;

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
        $ratio = $height / $this->getImageSize($this->loadedImage, 'height');
        $width = $this->getImageSize($this->loadedImage) * $ratio;

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
        $originalRatio = $this->getRatio($this->getImagesize($this->loadedImage), $this->getImageSize($this->loadedImage, 'height'));

        switch ($ratio) {
            case ($ratio < $originalRatio):
                return $this->resize($width, $height, ['resizeType' => ImageResizer::RESIZE_TYPE_WIDTH]);
            case ($ratio > $originalRatio):
                return $this->resize($width, $height, ['resizeType' => ImageResizer::RESIZE_TYPE_HEIGHT]);
            default:
                return $this->resize($width, $height, ['resizeType' => ImageResizer::RESIZE_TYPE_EXACT]);
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param array $dimensions
     * @return bool
     * @throws ProcessorException
     */
    protected function doResize($width = null, $height = null, $dimensions)
    {
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['height'];
        $newImage = ImageFactory::factory(null, ['width' => $width, 'height' => $height], 'image');

        list($red, $green, $blue) = $this->extractBackgroundColor();

        $background = imagecolorallocate($newImage, $red, $green, $blue);
        imagefilledrectangle($newImage, 0, 0, $width, $height, $background);

        $left = $this->getAxisOffset($width, $newWidth);
        $top = $this->getAxisOffset($height, $newHeight);

        imagecopyresampled($newImage, $this->loadedImage, $left, $top, 0, 0, $newWidth, $newHeight, $this->getImageSize($this->loadedImage), $this->getImageSize($this->loadedImage, 'height'));

        $this->processedImage = $newImage;
        return true;
    }

    /**
     * @return string|array
     * @throws ProcessorException
     */
    protected function extractBackgroundColor()
    {
        if (is_array($this->background) && count($this->background) == 3) {
            $background = $this->extractRgbColor($this->background);
        } else if ($this->background == ImageResizer::BACKGROUND_COLOR_AUTO) {
            $background = $this->extractAutoBackgroundColor();
        } else if (is_string($this->background)) {
            $background = $this->extractHexColor($this->background);
        } else {
            throw new ProcessorException('Invalid background color');
        }

        return $background;
    }

    /**
     * @param $colorArray
     * @return array
     */
    protected function extractRgbColor($colorArray)
    {
        return $colorArray;
    }

    /**
     * @param string $backgroundColor
     * @return array
     */
    protected function extractHexColor($backgroundColor)
    {
        $color = ltrim($backgroundColor, '#');

        return (strlen($backgroundColor) == 3) ? sscanf($color, "%1x%1x%1x") : sscanf($color, "%02x%02x%02x");
    }

    /**
     * Extract the color from the top left corner of the image
     *
     * @return array
     */
    protected function extractAutoBackgroundColor()
    {
        $thisColor = imagecolorat($this->loadedImage, 0, 0);
        $colorInfo = imagecolorsforindex($this->loadedImage, $thisColor);

        return [
            $colorInfo['red'],
            $colorInfo['green'],
            $colorInfo['blue']
        ];
    }

    /**
     * @param $big
     * @param $small
     * @return int
     */
    protected function getAxisOffset($big, $small)
    {
        $diff = $big - $small;
        $offset = 0;

        if ($diff <= 0) {
            return $offset;
        }

        switch ($this->resizePositionX) {
            case ImageResizer::RESIZE_POSITION_LEFT:
            case ImageResizer::RESIZE_POSITION_TOP:
                $offset = 0;
                break;
            case ImageResizer::RESIZE_POSITION_CENTER:
            case ImageResizer::RESIZE_POSITION_MIDDLE:
                $offset = ($diff) / 2;
                break;
            case ImageResizer::RESIZE_POSITION_RIGHT:
            case ImageResizer::RESIZE_POSITION_BOTTOM:
                $offset = $diff;
                break;
        }

        return (int)$offset;
    }

    /**
     * @param string $filename
     * @return string|null
     */
    public function getImageMimeType($filename = null)
    {
        if (empty($filename)) {
            return null;
        }

        $imageInfo = getimagesize($filename);

        if (is_array($imageInfo) && isset($imageInfo[2])) {
            return $imageInfo[2];
        }

        return null;
    }

    /**
     * @param resource $imageResource
     * @param string $size
     * @return int
     */
    public function getImageSize($imageResource, $size = 'width') {
        if($size == 'width') {
            return imagesx($imageResource);
        } else {
            return imagesy($imageResource);
        }
    }

}