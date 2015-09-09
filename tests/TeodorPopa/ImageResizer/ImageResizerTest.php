<?php

namespace TeodorPopa\ImageResizer;

use TeodorPopa\ImageResizer\Exceptions\ProcessorException;

class ImageResizerTest extends \PHPUnit_Framework_TestCase
{
    protected $images = [
        'jpg' => [
            'vertical' => 'tests/images/jpg_vertical.jpg',
            'horizontal' => 'tests/images/jpg_horizontal.jpg',
            'exact' => 'tests/images/jpg_exact.jpg',
        ],
        'png' => [
            'vertical' => 'tests/images/png_vertical.png',
            'horizontal' => 'tests/images/png_horizontal.png',
            'exact' => 'tests/images/png_exact.png',
        ],
    ];

    public function tearDown()
    {
        foreach (glob("images/test_*") as $filename) {
            @unlink($filename);
        }
    }

    /**
     * when trying to load an inexistent image throw exception
     */
    public function testWhenTryingToLoadAnInexistentImageThrowException()
    {
        try {
            $ImageResizer = ImageResizer::load('__NON_EXISTENT_IMAGE__.jpg');

            $this->fail('When sending an invalid image file should throw a RuntimeException');
        } catch(ProcessorException $e) {
            $this->assertEquals('The specified image does not exist.', $e->getMessage());
        }
    }

    /**
     * when loading existing images should return true
     */
    public function testWhenLoadingExistingImagesShouldReturnTrue()
    {
        try {
            foreach($this->images as $imageType) {
                foreach($imageType as $image) {
                    $ImageResizer = ImageResizer::load($image);
                }
            }
        } catch(\Exception $e) {
            $this->fail('Should not fail if all images exist on disk: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image should save the image on disk at the specified dimensions
     */
    public function testResizingAJpegImageShouldSaveTheImageOnDiskAtTheSpecifiedDimensions()
    {
        $filename = 'tests/images/test_jpg_vertical_exact_resize_10_100.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['vertical'])->resize(10, 100, [
                'resizeType' => ImageResizer::RESIZE_TYPE_EXACT
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(100, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a png image should save the image on disk at the specified dimensions
     */
    public function testResizingAPngImageShouldSaveTheImageOnDiskAtTheSpecifiedDimensions()
    {
        $filename = 'tests/images/test_png_vertical_exact_resize_10_100.png';
        try {
            $ImageResizer = ImageResizer::load($this->images['png']['vertical'])->resize(10, 100, [
                'resizeType' => ImageResizer::RESIZE_TYPE_EXACT
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(100, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type width should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeWidthShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_vertical_width_resize_10_x.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['vertical'])->resize(10, 1, [
                'resizeType' => ImageResizer::RESIZE_TYPE_WIDTH
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(1, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type height should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeHeightShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_x_20.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['horizontal'])->resize(1, 20, [
                'resizeType' => ImageResizer::RESIZE_TYPE_HEIGHT
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(1, $fileInfo[0]);
            $this->assertEquals(20, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type exact should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeExactShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_40_40.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['horizontal'])->resize(40, 40, [
                'resizeType' => ImageResizer::RESIZE_TYPE_EXACT
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(40, $fileInfo[0]);
            $this->assertEquals(40, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type auto should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeAutoShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_10_15.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['exact'])->resize(10, 15)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(15, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type auto and background color hex should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeAutoAndBackgroundColorHexShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_10_15_black.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['exact'])->resize(10, 15, [
                'background' => '#000000'
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(15, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type auto and background color rgb should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeAutoAndBackgroundColorRgbShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_10_15_red.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['exact'])->resize(10, 15, [
                'background' => [255, 0, 0]
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(15, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jpeg image with resize type auto and background color auto should save the image to disk
     */
    public function testResizingAJpegImageWithResizeTypeAutoAndBackgroundColorAutoShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_10_15_auto.jpg';
        try {
            $ImageResizer = ImageResizer::load($this->images['jpg']['exact'])->resize(10, 15, [
                'background' => ImageResizer::BACKGROUND_COLOR_AUTO
            ])->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(15, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }




}
