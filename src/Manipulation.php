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

namespace O2System\Image;

// ------------------------------------------------------------------------

use O2System\Image\Abstracts\AbstractDriver;
use O2System\Image\Datastructures\Config;
use O2System\Spl\Exceptions\Runtime\FileNotFoundException;

/**
 * Class Manipulation
 *
 * @package O2System\Image
 */
class Manipulation
{
    /**
     * Manipulation::ROTATE_CW
     *
     * Clock wise image rotation degrees.
     *
     * @var int
     */
    const ROTATE_CW = 90;

    /**
     * Manipulation::ROTATE_CCW
     *
     * Counter clock wise image rotation degrees.
     *
     * @var int
     */
    const ROTATE_CCW = -90;

    /**
     * Manipulation::FLIP_HORIZONTAL
     *
     * Flip image with horizontal axis.
     *
     * @var int
     */
    const FLIP_HORIZONTAL = 1;

    /**
     * Manipulation::FLIP_VERTICAL
     *
     * Flip image with vertical axis.
     *
     * @var int
     */
    const FLIP_VERTICAL = 2;

    /**
     * Manipulation::FLIP_BOTH
     *
     * Flip image with horizontal and vertical axis.
     *
     * @var int
     */
    const FLIP_BOTH = 3;

    /**
     * Manipulation::ORIENTATION_AUTO
     *
     * Auto image orientation.
     *
     * @var string
     */
    const ORIENTATION_AUTO = 'AUTO';

    /**
     * Manipulation::ORIENTATION_LANDSCAPE
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_LANDSCAPE = 'LANDSCAPE';

    /**
     * Manipulation::ORIENTATION_PORTRAIT
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_PORTRAIT = 'PORTRAIT';

    /**
     * Manipulation::ORIENTATION_SQUARE
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_SQUARE = 'SQUARE';

    /**
     * Manipulation::$config
     *
     * Manipulation image config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Manipulation::$driver
     *
     * Manipulation image driver.
     *
     * @var AbstractDriver
     */
    protected $driver;

    // ------------------------------------------------------------------------

    /**
     * Manipulation::__construct
     *
     * @param \O2System\Image\Datastructures\Config $config
     */
    public function __construct( Config $config )
    {
        $this->config = $config;

        if ( $this->config->offsetExists( 'driver' ) ) {
            $this->loadDriver( $this->config->driver );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::loadDriver
     *
     * Internal driver loader.
     *
     * @param string $driverOffset Driver offset name.
     *
     * @return bool
     */
    protected function loadDriver( $driverOffset )
    {
        $driverClassName = '\O2System\Image\Drivers\\' . ucfirst( $driverOffset ) . 'Driver';

        if ( class_exists( $driverClassName ) ) {
            if ( $this->config->offsetExists( $driverOffset ) ) {
                $config = $this->config[ $driverOffset ];
            } else {
                $config = $this->config->getArrayCopy();
            }

            if ( isset( $config[ 'engine' ] ) ) {
                unset( $config[ 'engine' ] );
            }

            $this->driver = new $driverClassName();

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::setDriver
     *
     * Manually set image manipulation library driver.
     *
     * @param \O2System\Image\Abstracts\AbstractDriver $imageDriver
     *
     * @return static
     */
    public function setDriver( AbstractDriver $imageDriver )
    {
        $this->driver = $imageDriver;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setImageFile
     *
     * Sets image for manipulation.
     *
     * @param string $imageFilePath Existing image file path.
     *
     * @return static
     */
    public function setImageFile( $imageFilePath )
    {
        if ( ! $this->driver->setSourceImage( $imageFilePath ) ) {
            throw new FileNotFoundException( 'E_IMAGE_FILE_NOT_FOUND' );
        }

        // Create image source resource
        $this->driver->createFromSource();

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setImageUrl( $imageUrl )
    {
        if( false === ( $imageString = file_get_contents( $imageUrl ) ) ) {
            throw new FileNotFoundException( 'E_IMAGE_URL_INVALID' );
        }

        // Create image source resource
        $this->driver->createFromString( $imageString );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setImageString
     *
     * @param string $imageString Image string.
     * @param bool   $base64 Use base64_decode to decode the image string.
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    public function setImageString( $imageString, $base64 = false )
    {
        if( $base64 ) {
            if( false === ( $imageString = base64_decode( $imageString, true ) ) ) {
                throw new FileNotFoundException( 'E_IMAGE_STRING_INVALID' );
            }
        }

        // Create image source resource
        $this->driver->createFromString( $imageString );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::rotateImage
     *
     * Rotate an image.
     *
     * @param int $degrees Image rotation degrees.
     *
     * @return bool
     */
    public function rotateImage( $degrees )
    {
        if ( is_int( $degrees ) ) {
            $this->driver->rotate( $degrees );

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::flipImage
     *
     * Flip an image.
     *
     * @param int $axis Image flip axis.
     */
    public function flipImage( $axis )
    {
        if ( in_array( $axis, [ self::FLIP_HORIZONTAL, self::FLIP_VERTICAL, self::FLIP_BOTH ] ) ) {

            $this->driver->flip( $axis );

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::resizeImage
     *
     * Scale an image using the given new width and height
     *
     * @param int    $newWidth    The width to scale the image to.
     * @param int    $newHeight   The height to scale the image to.
     * @param string $orientation Supported image orientation:
     *                            1. AUTO
     *                            2. LANDSCAPE
     *                            3. PORTRAIT
     *                            4. SQUARE
     *
     * @return bool
     */
    public function resizeImage( $newWidth, $newHeight )
    {
        $resampleImageFile = $this->driver->getSourceImageFile();
        $resampleDimension = $resampleImageFile->getDimension();
        $resampleDimension->maintainAspectRatio = $this->config->offsetGet( 'maintainAspectRatio' );

        if( $newWidth == $newHeight ) {
            $this->driver->setResampleImage( $resampleImageFile->withDimension(
                $resampleDimension
                    ->withOrientation( 'SQUARE' )
                    ->withFocus( 'CENTER' )
                    ->withSize( $newWidth, $newHeight )
            ) );
        } else {
            $this->driver->setResampleImage( $resampleImageFile->withDimension(
                $resampleDimension
                    ->withOrientation( $this->config->offsetGet( 'orientation' ) )
                    ->withFocus( $this->config->offsetGet( 'focus' ) )
                    ->withSize( $newWidth, $newHeight )
            ) );
        }

        return $this->driver->resize();
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::scaleImage
     *
     * Scale an image using the percentage image scale.
     *
     * @param int $newScale The percentage to scale the image to.
     *
     * @return bool
     */
    public function scaleImage( $newScale )
    {
        $resampleImageFile = $this->driver->getSourceImageFile();
        $resampleDimension = $resampleImageFile->getDimension();
        $resampleDimension->maintainAspectRatio = $this->config->offsetGet( 'maintainAspectRatio' );

        $this->driver->setResampleImage( $resampleImageFile->withDimension(
            $resampleDimension
                ->withScale( $newScale )
        ) );

        return $this->driver->scale();
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::displayImage
     *
     * Display an image.
     *
     * @return void
     */
    public function displayImage()
    {
        $this->driver->display( $this->config->offsetGet( 'quality' ) );
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::saveImage
     *
     * Save an manipulated image into new image file.
     *
     * @param string $saveImageFilePath Save image file path.
     *
     * @return bool
     */
    public function saveImage( $saveImageFilePath )
    {
        $this->driver->save( $saveImageFilePath, $this->config->offsetGet( 'quality' ) );
    }
}