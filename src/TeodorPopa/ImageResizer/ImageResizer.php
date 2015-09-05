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
class ImageResizer
{

    const RESIZE_TYPE_AUTO = 'auto';

    const RESIZE_TYPE_WIDTH = 'width';

    const RESIZE_TYPE_HEIGHT = 'height';

    const RESIZE_TYPE_EXACT = 'exact';

    /**
     * @var string
     */
    private $imageFile;

    /**
     * @var Resource
     */
    private $imageResource;

    /**
     * @var Resource
     */
    private $resizedImageResource;

    public function __construct($fileName)
    {
        $this->imageFile = $fileName;

        $this->testSettings();

        $this->loadImageResource();
    }

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

    protected function loadImageResource()
    {
        $imageInfo = $this->getImageInfo();

        if(!is_array($imageInfo) && !isset($imageInfo[2])) {
            throw new \RuntimeException('Cannot load the image resource.');
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

    protected function getImageInfo()
    {
        $imageInfo = getimagesize($this->imageFile);
        return $imageInfo;
    }

    protected function getImageWidth() {
        return imagesx($this->imageResource);
    }

    protected function getImageHeight() {
        return imagesy($this->imageResource);
    }

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

    public function resize($width, $height, $resizeType = self::RESIZE_TYPE_AUTO)
    {
        switch($resizeType) {
            case self::RESIZE_TYPE_WIDTH:
                $dimensions = $this->getResizeToWidthDimensions($width);
                $newImageResource = $this->doResize($dimensions);
                break;
            case self::RESIZE_TYPE_HEIGHT:
                $dimensions = $this->getResizeToHeightDimensions($height);
                $newImageResource = $this->doResize($dimensions);
                break;
            case self::RESIZE_TYPE_EXACT:
                $dimensions = $this->getResizeExactDimensions($width, $height);
                $newImageResource = $this->doResize($dimensions);
                break;
            default:
                $newImageResource = $this->resizeAuto($width, $height);
        }

        return $newImageResource;
    }

    protected function getResizeToWidthDimensions($width)
    {
        $ratio = $width / $this->getImageWidth();
        $height = $this->getImageHeight() * $ratio;

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    protected function getResizeToHeightDimensions($height)
    {
        $ratio = $height / $this->getImageHeight();
        $width = $this->getImageWidth() * $ratio;

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    protected function getResizeExactDimensions($width, $height)
    {
        return [
            'width' => $width,
            'height' => $height
        ];
    }

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

    protected function doResize($dimensions)
    {
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['newHeight'];
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled($newImage, $this->imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $this->getImageWidth(), $this->getImageHeight());


        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $background = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $background);

        imagecopyresampled($resizedImage, $newImage, 0, 0, 0, 0, $newWidth, $newHeight, $newWidth, $newHeight);

        $this->resizedImageResource = $resizedImage;
        return $this->resizedImageResource;
    }

    public function output($imageType = IMAGETYPE_JPEG)
    {
        switch($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->resizedImageResource);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->resizedImageResource);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->resizedImageResource);
                break;
        }
    }

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