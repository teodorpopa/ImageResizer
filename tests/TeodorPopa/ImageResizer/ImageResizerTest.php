<?php

namespace TeodorPopa\ImageResizer;

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
            $ImageResizer = new ImageResizer('__NON_EXISTENT_IMAGE__.jpg');

            $this->fail('When sending an invalid image file should throw a RuntimeException');
        } catch(\RuntimeException $e) {
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
                    $ImageResizer = new ImageResizer($image);
                }
            }
        } catch(\Exception $e) {
            $this->fail('Should not fail if all images exist on disk');
        }
    }

    /**
     * resizing a jpeg image should save the image on disk at the specified dimensions
     */
    public function testResizingAJpegImageShouldSaveTheImageOnDiskAtTheSpecifiedDimensions()
    {
        $filename = 'tests/images/test_jpg_vertical_exact_resize_10_100.jpg';
        try {
            $ImageResizer = new ImageResizer($this->images['jpg']['vertical']);
            $ImageResizer->resize(10, 100, ImageResizer::RESIZE_TYPE_EXACT)->save($filename);

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
            $ImageResizer = new ImageResizer($this->images['png']['vertical']);
            $ImageResizer->resize(10, 100, ImageResizer::RESIZE_TYPE_EXACT)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(100, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jepg image with resize type width should save the image to disk
     */
    public function testResizingAJepgImageWithResizeTypeWidthShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_vertical_width_resize_10_x.jpg';
        try {
            $ImageResizer = new ImageResizer($this->images['jpg']['vertical']);
            $ImageResizer->resize(10, null, ImageResizer::RESIZE_TYPE_WIDTH)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(10, $fileInfo[0]);
            $this->assertEquals(166, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jepg image with resize type height should save the image to disk
     */
    public function testResizingAJepgImageWithResizeTypeHeightShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_x_20.jpg';
        try {
            $ImageResizer = new ImageResizer($this->images['jpg']['horizontal']);
            $ImageResizer->resize(null, 20, ImageResizer::RESIZE_TYPE_HEIGHT)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(333, $fileInfo[0]);
            $this->assertEquals(20, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jepg image with resize type exact should save the image to disk
     */
    public function testResizingAJepgImageWithResizeTypeExactShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_40_40.jpg';
        try {
            $ImageResizer = new ImageResizer($this->images['jpg']['horizontal']);
            $ImageResizer->resize(40, 40, ImageResizer::RESIZE_TYPE_EXACT)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(40, $fileInfo[0]);
            $this->assertEquals(40, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }

    /**
     * resizing a jepg image with resize type auto should save the image to disk
     */
    public function testResizingAJepgImageWithResizeTypeAutoShouldSaveTheImageToDisk()
    {
        $filename = 'tests/images/test_jpg_horizontal_height_resize_40_4.jpg';
        try {
            $ImageResizer = new ImageResizer($this->images['jpg']['exact']);
            $ImageResizer->resize(40, 4)->save($filename);

            $this->assertFileExists($filename);

            $fileInfo = getimagesize($filename);
            $this->assertEquals(4, $fileInfo[0]);
            $this->assertEquals(4, $fileInfo[1]);

        } catch(\Exception $e) {
            $this->fail('Should not fail if resize is done correctly: ' . $e->getMessage());
        }
    }




}
