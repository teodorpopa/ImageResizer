<?php
/**
 * ImageResizer
 *
 * @package TeodorPopa\ImageResizer
 * @copyright  Copyright (c) 2015 Teodor Popa (popa.teodor@gmail.com)
 */
namespace TeodorPopa\ImageResizer;

/**
 * ImageResizer
 *
 * Resize an image to specified dimensions
 *
 * @package TeodorPopa\ImageResizer
 */
/**
 * Class ImageResizer
 * @package TeodorPopa\ImageResizer
 */
class ImageResizer
{
    /**
     * Resize done to keep image proportions to the best fit
     * The resized image may not have the exact width and height specified
     */
    const RESIZE_TYPE_AUTO = 'auto';

    /**
     * Height is automatically calculated based on the width
     */
    const RESIZE_TYPE_WIDTH = 'width';

    /**
     * Width is automatically calculated based on the height
     */
    const RESIZE_TYPE_HEIGHT = 'height';

    /**
     * Image resized to the exact width and height specified
     */
    const RESIZE_TYPE_EXACT = 'exact';

    /**
     * @var string
     */
    private $imageFile;

    /**
     * @var resource
     */
    private $imageResource;

    /**
     * @var resource
     */
    private $resizedImageResource;

    /**
     * @param string $fileName
     * @throws \RuntimeException
     */
    public function __construct($fileName)
    {
        $this->imageFile = $fileName;

        $this->testSettings();

        $this->loadImageResource();
    }

    /**
     * Test basic settings to handle and manipulate an image
     *
     * @throws \RuntimeException
     * @return bool
     */
    protected function testSettings()
    {
        if (ini_get('allow_url_fopen') == '0') {
            throw new \RuntimeException('Please enable allow_url_fopen.');
        }

        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD Extension is not loaded.');
        }

        if (!file_exists($this->imageFile)) {
            throw new \RuntimeException('The specified image does not exist.');
        }

        return true;
    }

    /**
     * Load the image resource
     * @throws \Exception
     */
    protected function loadImageResource()
    {
        $imageInfo = $this->getImageInfo();

        if(!is_array($imageInfo) && !isset($imageInfo[2])) {
            throw new \Exception('Cannot load the image resource.');
        }

        $imageType = $imageInfo[2];

        switch($imageType) {
            case IMAGETYPE_JPEG:
                $resource = imagecreatefromjpeg($this->imageFile);
                break;
            case IMAGETYPE_PNG:
                $resource = imagecreatefrompng($this->imageFile);
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                break;
            case IMAGETYPE_GIF:
                $resource = imagecreatefromgif($this->imageFile);
                break;
            default:
                $resource = null;
                break;
        }

        $this->imageResource = $resource;
    }

    /**
     * Get a file extension
     *
     * @param string $fileName
     * @return string
     * @throws \Exception
     */
    public function getExtension($fileName)
    {
        if(!isset($fileName)) {
            throw new \Exception('Please provide an image filename.');
        }

        return end(explode(".", $fileName));
    }

    /**
     * @return array
     */
    protected function getImageInfo()
    {
        $imageInfo = getimagesize($this->imageFile);
        return $imageInfo;
    }

    /**
     * @return int
     */
    protected function getImageWidth() {
        return imagesx($this->imageResource);
    }

    /**
     * @return int
     */
    protected function getImageHeight() {
        return imagesy($this->imageResource);
    }

    /**
     * Calculate the image ratio
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function getRatio($width = null, $height = null)
    {
        if (!empty($width) && !empty($height)) {
            $w = (int)$width;
            $h = (int)$height;
        } else if(!empty($this->getImageWidth()) && !empty($this->getImageHeight())) {
            $w = (int)$this->getImageWidth();
            $h = (int)$this->getImageHeight();
        } else {
            throw new \RuntimeException('Cannot calculate the image ratio');
        }

        return number_format(($w / $h), 2, '.', '');
    }

    /**
     * Resize the image resource
     * $resizeType can be one of:
     * - RESIZE_TYPE_AUTO (default)
     * - RESIZE_TYPE_WIDTH
     * - RESIZE_TYPE_HEIGHT
     * - RESIZE_TYPE_EXACT
     *
     * @param $width
     * @param $height
     * @param string $resizeType
     * @return ImageResizer
     */
    public function resize($width, $height, $resizeType = self::RESIZE_TYPE_AUTO)
    {
        switch($resizeType) {
            case self::RESIZE_TYPE_WIDTH:
                $dimensions = $this->getResizeToWidthDimensions($width);
                $this->doResize($dimensions);
                break;
            case self::RESIZE_TYPE_HEIGHT:
                $dimensions = $this->getResizeToHeightDimensions($height);
                $this->doResize($dimensions);
                break;
            case self::RESIZE_TYPE_EXACT:
                $dimensions = $this->getResizeExactDimensions($width, $height);
                $this->doResize($dimensions);
                break;
            default:
                $this->resizeAuto($width, $height);
        }

        return $this;
    }

    /**
     * Get the dimensions array for resizing to exact width
     *
     * @param int $width
     * @return array
     */
    protected function getResizeToWidthDimensions($width)
    {
        $ratio = $width / $this->getImageWidth();
        $height = $this->getImageHeight() * $ratio;

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
        $ratio = $height / $this->getImageHeight();
        $width = $this->getImageWidth() * $ratio;

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

        switch($ratio) {
            case ($ratio < $this->getRatio()):
                return $this->resize($width, $height, self::RESIZE_TYPE_WIDTH);
                break;
            case ($ratio > $this->getRatio()):
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
    protected function doResize($dimensions)
    {
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['height'];
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled($newImage, $this->imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $this->getImageWidth(), $this->getImageHeight());


        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $background = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $background);

        imagecopyresampled($resizedImage, $newImage, 0, 0, 0, 0, $newWidth, $newHeight, $newWidth, $newHeight);

        $this->resizedImageResource = $resizedImage;
        return true;
    }

    /**
     * Output image on screen
     *
     * @param int $imageType
     */
    public function output($imageType = IMAGETYPE_JPEG)
    {
        $image = (empty($this->resizedImageResource)) ? $this->imageResource : $this->resizedImageResource;

        switch($imageType) {
            case IMAGETYPE_JPEG:
                header("Content-type: image/jpeg");
                imagejpeg($image);
                break;
            case IMAGETYPE_PNG:
                header("Content-type: image/png");
                imagepng($image);
                break;
            case IMAGETYPE_GIF:
                header("Content-type: image/gif");
                imagegif($image);
                break;
        }

        return true;
    }

    /**
     * Save the file to specified location
     *
     * @param string $filename
     * @param int $quality 1-10
     * @return bool
     */
    public function save($filename, $quality = 8)
    {
        $imageInfo = $this->getImageInfo();

        if(!isset($imageInfo['mime'])) {
            $imageType = 'image/jpeg';
        } else {
            $imageType = $imageInfo['mime'];
        }

        switch($imageType) {
            case 'image/jpeg':
                $compression = $quality * 10;
                imagejpeg($this->resizedImageResource, $filename, $compression);
                break;
            case 'image/png':
                $quality--;
                imagepng($this->resizedImageResource, $filename, $quality);
                break;
            case 'image/gif':
                imagegif($this->resizedImageResource, $filename);
        }

        return true;
    }


}
