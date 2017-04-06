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
use O2System\Image\Abstracts\AbstractWatermark;
use O2System\Image\Dimension;
use O2System\Image\Watermark\Overlay;
use O2System\Image\Watermark\Text;

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
     * @return bool
     */
    public function createFromString( $imageString )
    {
        $this->sourceImageResource = new \Imagick();

        try {

            return $this->sourceImageResource->readImageBlob( $imageString );

        } catch ( \ImagickException $e ) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return bool
     */
    public function rotate( $degrees )
    {
        $resampleImageResource =& $this->getResampleImageResource();

        return $resampleImageResource->rotateImage( '#000000', $degrees );
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return bool
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

            try {

                switch ( $axis ) {
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

                return true;

            } catch ( \ImagickException $e ) {
                $this->errors[ $e->getCode() ] = $e->getMessage();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::resize
     *
     * Resize an image using the given new width and height.
     *
     * @return bool
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
            if ( $resampleImageResource->resizeImage( $resizeWidth, $resizeHeight, \Imagick::FILTER_LANCZOS, 0.9,
                true )
            ) {
                $resampleAxis = new Dimension\Axis(
                    ( $resizeWidth - $resampleDimension->getWidth() ) / 2,
                    ( $resizeHeight - $resampleDimension->getWidth() ) / 2
                );
            }
        } else {
            switch ( $resampleDimension->getFocus() ) {
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

            if ( ! $resampleImageResource->resizeImage(
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight(),
                \Imagick::FILTER_CATROM, 0.9, true )
            ) {
                return false;
            }
        }

        if ( isset( $resampleAxis ) ) {
            return $this->crop( $resampleDimension->withAxis( $resampleAxis ) );
        }

        return false;
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

        try {
            return $resampleImageResource->scaleImage( $resampleDimension->getWidth(), $resampleDimension->getHeight(),
                true );
        } catch ( \ImagickException $e ) {
            $this->errors[ $e->getCode() ] = $e->getMessage();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::crop
     *
     * Crop an image.
     *
     * @param \O2System\Image\Dimension $dimension
     *
     * @return bool
     */
    public function crop( Dimension $dimension )
    {
        $resampleImageResource =& $this->getResampleImageResource();

        try {
            return $resampleImageResource->cropImage(
                $dimension->getWidth(),
                $dimension->getHeight(),
                $dimension->getAxis()->getX(),
                $dimension->getAxis()->getY()
            );
        } catch ( \ImagickException $e ) {
            $this->errors[ $e->getCode() ] = $e->getMessage();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ImagemagickDriver::watermark
     *
     * Watermark an image.
     *
     * @param \O2System\Image\Abstracts\AbstractWatermark $watermark
     *
     * @return bool
     */
    public function watermark( AbstractWatermark $watermark )
    {
        $resampleImageResource =& $this->getResampleImageResource();

        if ( $watermark instanceof Text ) {

            $draw = new \ImagickDraw();
            $draw->setFont( $watermark->getFontPath() );
            $draw->setFontSize( $watermark->getFontSize() );
            $draw->setFillColor( $watermark->getFontColor() );

            if ( false !== ( $textAxis = $watermark->getAxis() ) ) {
                $draw->annotation( $textAxis->getX(), $textAxis->getY(), $watermark->getString() );
            } else {
                switch ( $watermark->getPosition() ) {
                    default:
                    case 'MIDDLE_MIDDLE':
                    case 'MIDDLE':
                    case 'CENTER':
                        $draw->setGravity( \Imagick::GRAVITY_CENTER );
                        break;

                    case 'MIDDLE_LEFT':
                        $draw->setGravity( \Imagick::GRAVITY_WEST );
                        break;

                    case 'MIDDLE_RIGHT':
                        $draw->setGravity( \Imagick::GRAVITY_EAST );
                        break;

                    case 'MIDDLE_TOP':
                        $draw->setGravity( \Imagick::GRAVITY_NORTH );
                        break;

                    case 'MIDDLE_BOTTOM':
                        $draw->setGravity( \Imagick::GRAVITY_SOUTH );
                        break;

                    case 'TOP_LEFT':
                        $draw->setGravity( \Imagick::GRAVITY_NORTHWEST );
                        break;

                    case 'TOP_RIGHT':
                        $draw->setGravity( \Imagick::GRAVITY_NORTHEAST );
                        break;

                    case 'BOTTOM_LEFT':
                        $draw->setGravity( \Imagick::GRAVITY_SOUTHWEST );
                        break;

                    case 'BOTTOM_RIGHT':
                        $draw->setGravity( \Imagick::GRAVITY_SOUTHEAST );
                        break;
                }
            }

            return $resampleImageResource->annotateImage(
                $draw,
                $watermark->getPadding(),
                $watermark->getPadding(),
                $watermark->getAngle(),
                $watermark->getString()
            );
        } elseif ( $watermark instanceof Overlay ) {
            $watermarkImage = new self;
            $watermarkImage->setSourceImage( $watermark->getImagePath() );
            $watermarkImage->createFromSource();

            $watermarkImageFile = $watermarkImage->getSourceImageFile();
            $watermarkImageDimension = $watermarkImageFile->getDimension();
            $watermarkImageDimension->maintainAspectRatio = true;

            $resampleImageDimension = $this->resampleImageFile->getDimension();

            if ( false === ( $scale = $watermark->getImageScale() ) ) {
                $scale = min(
                    round( ( ( $resampleImageDimension->getWidth() / 2 ) / $watermarkImageDimension->getWidth() ) * 100 ),
                    round( ( ( $resampleImageDimension->getHeight() / 2 ) / $watermarkImageDimension->getHeight() ) * 100 )
                );
            }

            if ( $scale > 0 ) {
                $watermarkImage->setResampleImage( $watermarkImageFile->withDimension(
                    $watermarkImageDimension
                        ->withScale( $scale )
                ) );
            }

            $watermarkImageDimension = $watermarkImage->getResampleImageFile()->getDimension();

            if ( $watermarkImage->scale() ) {
                $watermarkImageResource = $watermarkImage->getResampleImageResource();

                if ( false !== ( $watermarkAxis = $watermark->getAxis() ) ) {
                    $watermarkImageAxisX = $watermarkAxis->getX();
                    $watermarkImageAxisY = $watermarkAxis->getY();
                } else {
                    switch ( $watermark->getPosition() ) {
                        default:
                        case 'MIDDLE_MIDDLE':
                        case 'MIDDLE':
                        case 'CENTER':
                            $watermarkImageAxisX = ( $resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth() ) / 2;
                            $watermarkImageAxisY = ( $resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight() ) / 2;
                            break;

                        case 'MIDDLE_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = ( $resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight() ) / 2;
                            break;

                        case 'MIDDLE_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ( $watermarkImageDimension->getWidth() + $watermark->getPadding() );
                            $watermarkImageAxisY = ( $resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight() ) / 2;
                            break;

                        case 'MIDDLE_TOP':
                            $watermarkImageAxisX = ( $resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth() ) / 2;
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'MIDDLE_BOTTOM':
                            $watermarkImageAxisX = ( $resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth() ) / 2;
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - ( $watermarkImageDimension->getHeight() + $watermark->getPadding() );
                            break;

                        case 'TOP_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'TOP_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ( $watermarkImageDimension->getWidth() + $watermark->getPadding() );
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'BOTTOM_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'BOTTOM_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ( $watermarkImageDimension->getWidth() + $watermark->getPadding() );
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - ( $watermarkImageDimension->getHeight() + $watermark->getPadding() );
                            break;
                    }
                }

                try {
                    $resampleImageResource->compositeImage( $watermarkImageResource, \Imagick::COMPOSITE_OVER,
                        $watermarkImageAxisX,
                        $watermarkImageAxisY );

                    return true;
                } catch ( \ImagickException $e ) {
                    $this->errors[ $e->getCode() ] = $e->getMessage();
                }
            }
        }

        return false;
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
     * ImagemagickDriver::blob
     *
     * Returns image string blob.
     *
     * @return string
     */
    public function blob( $quality = 100 )
    {
        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->setCompressionQuality( $quality );

        return $resampleImageResource->getImageBlob();
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