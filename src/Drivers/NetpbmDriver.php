<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Image\Drivers;

// ------------------------------------------------------------------------

use O2System\Image\Abstracts\AbstractDriver;
use O2System\Image\Dimension;

/**
 * Class NetpbmDriver
 *
 * @package O2System\Image\Drivers
 */
class NetpbmDriver extends AbstractDriver
{
    /**
     * NetpbmDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return static
     */
    public function createFromSource()
    {
        // TODO: Implement createFromSource() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::createFromString
     *
     * Create an image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return void
     */
    public function createFromString( $imageString )
    {
        // TODO: Implement createFromString() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return void
     */
    public function rotate( $degrees )
    {
        // TODO: Implement rotate() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::resize
     *
     * Resize an image using the given new width and height.
     *
     * @return void
     */
    public function resize()
    {
        // TODO: Implement resize() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    public function scale()
    {
        // TODO: Implement scale() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return void
     */
    public function flip( $axis )
    {
        // TODO: Implement flip() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::display
     *
     * Display an image.
     *
     * @return void
     */
    public function display( $quality = 100 )
    {
        // TODO: Implement display() method.
    }

    // ------------------------------------------------------------------------

    /**
     * NetpbmDriver::save
     *
     * Save an image.
     *
     * @param string $imageTargetFilePath
     * @param int    $quality
     *
     * @return bool
     */
    public function save( $imageTargetFilePath, $quality = 100 )
    {
        // TODO: Implement save() method.
    }
}