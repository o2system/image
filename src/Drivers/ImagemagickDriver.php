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
 * Class ImagemagickDriver
 *
 * @package O2System\Image\Drivers
 */
class ImagemagickDriver extends AbstractDriver
{
    /**
     * ImagemagickDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return static
     */
    public function createFromSource()
    {
        $this->sourceImageResource = new \Imagick( $this->sourceImageFile->getRealPath() );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::createFromString
     *
     * Create an image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return void
     */
    public function createFromString( $imageString )
    {
        $this->sourceImageResource = new \Imagick();
        $this->sourceImageResource->readImageBlob( $imageString );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return void
     */
    public function rotate( $degrees )
    {
        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->rotateImage( '#000000', $degrees );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::resize
     *
     * Resize an image using the given new width and height.
     *
     * @return void
     */
    public function resize()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        $resizeWidth = $resampleDimension->getWidth() > $sourceDimension->getWidth() ? $sourceDimension->getWidth() : $resampleDimension->getWidth();
        $resizeHeight = $resampleDimension->getHeight() > $sourceDimension->getHeight() ? $sourceDimension->getHeight() : $resampleDimension->getHeight();

        if ( $sourceDimension->getOrientation() === 'LANDSCAPE' ) {
            $resizeWidth = round( $sourceDimension->getWidth() * $resampleDimension->getHeight() / $sourceDimension->getHeight() );
            $resizeHeight = $resampleDimension->getHeight();
        } elseif ( $sourceDimension->getOrientation() === 'PORTRAIT' ) {
            $resizeWidth = $resampleDimension->getWidth();
            $resizeHeight = round( $sourceDimension->getHeight() * $resampleDimension->getWidth() / $sourceDimension->getWidth() );
        }

        $resampleImageResource =& $this->getResampleImageResource();

        if ( $resampleDimension->getOrientation() === 'SQUARE' ) {
            $resampleImageResource->resizeImage( $resizeWidth, $resizeHeight, \Imagick::FILTER_LANCZOS, 0.9, true );
            $resampleAxis = new Dimension\Axis(
                ( $resizeWidth - $resampleDimension->getWidth() ) / 2,
                ( $resizeHeight - $resampleDimension->getWidth() ) / 2
            );
        } else {
            switch ( $resampleDimension->getQuadrant() ) {
                default:
                case 'CENTER':
                    $resampleAxis = new Dimension\Axis(
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
                case 'NORTH':
                    $resampleAxis = new Dimension\Axis(
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        0
                    );
                    break;
                case 'NORTHWEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        0
                    );
                    break;
                case 'NORTHEAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        0
                    );
                    break;
                case 'SOUTH':
                    $resampleAxis = new Dimension\Axis(
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHWEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHEAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'WEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
                case 'EAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
            }

            $resampleImageResource->resizeImage( $sourceDimension->getWidth(), $sourceDimension->getHeight(), \Imagick::FILTER_CATROM, 0.9, true );
        }

        $resampleImageResource->cropImage(
            $resampleDimension->getWidth(),
            $resampleDimension->getHeight(),
            $resampleAxis->getX(),
            $resampleAxis->getY()
        );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    public function scale()
    {
        $resampleDimension = $this->resampleImageFile->getDimension();

        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->scaleImage( $resampleDimension->getWidth(), $resampleDimension->getHeight(), true );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return void
     */
    public function flip( $axis )
    {
        $gdAxis = [
            1 => IMG_FLIP_HORIZONTAL,
            2 => IMG_FLIP_VERTICAL,
            3 => IMG_FLIP_BOTH,
        ];

        if ( array_key_exists( $axis, $gdAxis ) ) {
            $resampleImageResource =& $this->getResampleImageResource();

            switch ( $axis )
            {
                case 1:
                    $resampleImageResource->flopImage();
                    break;
                case 2:
                    $resampleImageResource->flipImage();
                    break;
                case 3:
                    $resampleImageResource->flopImage();
                    $resampleImageResource->flipImage();
                    break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::display
     *
     * Display an image.
     *
     * @return void
     */
    public function display( $quality = 100 )
    {
        header( 'Content-Disposition: filename=' . $this->sourceImageFile->getBasename() );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time() ) . ' GMT' );
        header( 'Content-Type: ' . $this->sourceImageFile->getMime() );

        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->setCompressionQuality( $quality );
        echo $resampleImageResource->getImageBlob();

        exit( EXIT_SUCCESS );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::save
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
        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->setCompressionQuality( $quality );

        return (bool)$resampleImageResource->writeImage( $imageTargetFilePath );
    }
}