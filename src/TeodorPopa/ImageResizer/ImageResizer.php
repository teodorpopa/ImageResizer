<?php

namespace TeodorPopa\ImageResizer;

use TeodorPopa\ImageResizer\Processors\GdProcessor;
use TeodorPopa\ImageResizer\Exceptions\SetupException;

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
     * Top position on the Y axis
     */
    const RESIZE_POSITION_TOP = 'top';

    /**
     * Middle position on the Y axis
     */
    const RESIZE_POSITION_MIDDLE = 'middle';

    /**
     * Bottom position on the Y axis
     */
    const RESIZE_POSITION_BOTTOM = 'bottom';

    /**
     * Left position on the X axis
     */
    const RESIZE_POSITION_LEFT = 'left';

    /**
     * Center position on the X axis
     */
    const RESIZE_POSITION_CENTER = 'center';

    /**
     * Right position on the X axis
     */
    const RESIZE_POSITION_RIGHT = 'right';

    /**
     * Auto extract the background color to fill the image
     */
    const BACKGROUND_COLOR_AUTO = 'background-auto';

    /**
     * Load an image into the processor
     *
     * @param string $filename
     * @return GdProcessor
     * @throws SetupException
     */
    public static function load($filename = null)
    {
        if (extension_loaded('gd')) {
            return new GdProcessor($filename);
        }

        throw new SetupException('To run this you need GD extensions to be installed.');
    }

}