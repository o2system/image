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
 * Class GdDriver
 *
 * @package O2System\Image\Drivers
 */
class GdDriver extends AbstractDriver
{
    /**
     * GdDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return void
     */
    public function createFromSource()
    {
        /**
         * A work-around for some improperly formatted, but
         * usable JPEGs; known to be produced by Samsung
         * smartphones' front-facing cameras.
         *
         * @see    https://bugs.php.net/bug.php?id=72404
         */
        ini_set( 'gd.jpeg_ignore_warning', 1 );

        $mime = $this->sourceImageFile->getMime();

        switch ( $mime ) {
            case 'image/jpg':
            case 'image/jpeg':
                $this->sourceImageResource = imagecreatefromjpeg( $this->sourceImageFile->getRealPath() );
                break;

            case 'image/gif':
                $this->sourceImageResource = imagecreatefromgif( $this->sourceImageFile->getRealPath() );
                break;

            case 'image/png':
                $this->sourceImageResource = imagecreatefrompng( $this->sourceImageFile->getRealPath() );
                break;
        }

        // Convert pallete images to true color images
        imagepalettetotruecolor( $this->sourceImageResource );
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::createFromString
     *
     * Create image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return void
     */
    public function createFromString( $imageString )
    {
        $this->sourceImageResource = imagecreatefromstring( $imageString );
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::rotate
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

        // Set the background color
        // This won't work with transparent PNG files so we are
        // going to have to figure out how to determine the color
        // of the alpha channel in a future release.
        $alphaChannel = imagecolorallocate( $resampleImageResource, 255, 255, 255 );

        // Rotate it!
        $this->resampleImageResource = imagerotate( $resampleImageResource, $degrees, $alphaChannel );
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::flip
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
            imageflip( $resampleImageResource, $axis );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::resize
     *
     * Resize an image using the given new width and height
     *
     * @return bool
     */
    public function resize()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        if ( $resampleDimension->getOrientation() === 'SQUARE' ) {
            if ( $sourceDimension->getOrientation() === 'LANDSCAPE' ) {
                $sourceSquareSize = $sourceDimension->getHeight();

                $sourceDimension = new Dimension(
                    $sourceSquareSize,
                    $sourceSquareSize,
                    ( $sourceDimension->getWidth() - $sourceDimension->getHeight() ) / 2,
                    0
                );
            } elseif ( $sourceDimension->getOrientation() === 'PORTRAIT' ) {
                $sourceSquareSize = $sourceDimension->getWidth();

                $sourceDimension = new Dimension(
                    $sourceSquareSize,
                    $sourceSquareSize,
                    0,
                    ( $sourceDimension->getHeight() - $sourceDimension->getWidth() ) / 2
                );
            }
        } else {

            $resizeWidth = $resampleDimension->getWidth() > $sourceDimension->getWidth() ? $sourceDimension->getWidth() : $resampleDimension->getWidth();
            $resizeHeight = $resampleDimension->getHeight() > $sourceDimension->getHeight() ? $sourceDimension->getHeight() : $resampleDimension->getHeight();

            if ( $sourceDimension->getOrientation() === 'LANDSCAPE' ) {
                $resizeWidth = round( $sourceDimension->getWidth() * $resampleDimension->getHeight() / $sourceDimension->getHeight() );
                $resizeHeight = $resampleDimension->getHeight();
            } elseif ( $sourceDimension->getOrientation() === 'PORTRAIT' ) {
                $resizeWidth = $resampleDimension->getWidth();
                $resizeHeight = round( $sourceDimension->getHeight() * $resampleDimension->getWidth() / $sourceDimension->getWidth() );
            }

            switch ( $resampleDimension->getQuadrant() ) {
                default:
                case 'CENTER':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
                case 'NORTH':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        0
                    );
                    break;
                case 'NORTHWEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        0
                    );
                    break;
                case 'NORTHEAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        0
                    );
                    break;
                case 'SOUTH':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ( $sourceDimension->getWidth() - $resizeWidth ) / 2,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHWEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHEAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'WEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
                case 'EAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        ( $sourceDimension->getHeight() - $resizeHeight ) / 2
                    );
                    break;
            }
        }

        $resampleAxis = $this->resampleImageFile->getDimension()->getAxis();
        $sourceAxis = $sourceDimension->getAxis();

        if ( function_exists( 'imagecreatetruecolor' ) ) {
            $this->resampleImageResource = imagecreatetruecolor( $resampleDimension->getWidth(),
                $resampleDimension->getHeight() );

            return imagecopyresampled(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        } else {
            $this->resampleImageResource = imagecreate( $resampleDimension->getWidth(),
                $resampleDimension->getHeight() );

            return imagecopyresized(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    public function scale()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        $resampleAxis = $this->resampleImageFile->getDimension()->getAxis();
        $sourceAxis = $sourceDimension->getAxis();

        if ( function_exists( 'imagecreatetruecolor' ) ) {
            $this->resampleImageResource = imagecreatetruecolor( $resampleDimension->getWidth(),
                $resampleDimension->getHeight() );

            return imagecopyresampled(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        } else {
            $this->resampleImageResource = imagecreate( $resampleDimension->getWidth(),
                $resampleDimension->getHeight() );

            return imagecopyresized(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::display
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

        $resampleImageResource =& $this->getResampleImageResource();

        switch ( $this->sourceImageFile->getMime() ) {
            case 'image/jpg':
            case 'image/jpeg':
                header( 'Content-Type: image/jpeg' );
                imagejpeg( $resampleImageResource, null, $quality );
                break;

            case 'image/gif':
                header( 'Content-Type: image/gif' );
                imagegif( $resampleImageResource );
                break;

            case 'image/png':
                header( 'Content-Type: image/png' );
                imagealphablending( $resampleImageResource, false );
                imagesavealpha( $resampleImageResource, true );
                imagepng( $resampleImageResource );
                break;
        }

        exit( EXIT_SUCCESS );
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::save
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

        switch ( $this->sourceImageFile->getMime() ) {
            case 'image/jpg':
            case 'image/jpeg':
                return (bool)@imagejpeg( $resampleImageResource, $imageTargetFilePath, $quality );
                break;

            case 'image/gif':
                return (bool)@imagegif( $resampleImageResource, $imageTargetFilePath );
                break;

            case 'image/png':
                imagealphablending( $resampleImageResource, false );
                imagesavealpha( $resampleImageResource, true );

                return (bool)@imagepng( $resampleImageResource, $imageTargetFilePath );
                break;
        }

        return false;
    }
}