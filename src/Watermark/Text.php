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

namespace O2System\Image\Watermark;

// ------------------------------------------------------------------------

use O2System\Image\Abstracts\AbstractWatermark;

/**
 * Class Text
 *
 * @package O2System\Image\Watermark
 */
class Text extends AbstractWatermark
{
    /**
     * Text::$fontTruetype
     *
     * Text use truetype font flag.
     *
     * @var bool
     */
    protected $fontTruetype = true;

    /**
     * Text::$fontPath
     *
     * Text font path.
     *
     * @var string
     */
    protected $fontPath;

    /**
     * Text::$fontSize
     *
     * Text font size.
     *
     * @var int
     */
    protected $fontSize;

    /**
     * Text::$fontColor
     *
     * Text font color.
     *
     * @var string
     */
    protected $fontColor = 'ffffff';

    /**
     * Text::$string
     *
     * Text string content.
     *
     * @var string
     */
    protected $string;

    /**
     * AbstractWatermark::$angle
     *
     * Text angle.
     *
     * @var int
     */
    protected $angle = 0;

    // ------------------------------------------------------------------------

    public function setFontPath( $fontPath )
    {
        if ( is_file( $fontPath ) ) {
            $this->fontTruetype = true;
            $this->fontPath = $fontPath;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setFontSize( $fontSize )
    {
        $this->fontSize = (int)$fontSize;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setFontColor( $fontColor )
    {
        $this->fontColor = '#' . ltrim( $fontColor, '#' );

        return $this;
    }

    // -------------------------------------------------------------------------

    public function setString( $string )
    {
        $this->string = trim( $string );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::signature
     *
     * Create a signature text image watermark with Jellyka Saint-Andrew's Queen Truetype Font.
     *
     * @param $string
     *
     * @return static
     */
    public function signature( $string, $size = 25, $color = 'ffffff' )
    {
        $this->setFontPath( __DIR__ . DIRECTORY_SEPARATOR . 'Fonts/Jellyka_Saint-Andrew\'s_Queen.ttf' )
            ->setFontSize( $size )
            ->setFontColor( $color )
            ->setString( $string );

        if ( $this->position === 'AUTO' ) {
            $this->position = 'CENTER';
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function copyright( $string, $size = 8, $color = 'ffffff' )
    {
        $this->setFontPath( __DIR__ . DIRECTORY_SEPARATOR . 'Fonts/ExpresswayRg-Regular.ttf' )
            ->setFontSize( $size )
            ->setFontColor( $color )
            ->setString( $string );

        if ( $this->position === 'AUTO' ) {
            $this->position = 'BOTTOM_LEFT';
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function getFontPath()
    {
        return $this->fontPath;
    }

    // ------------------------------------------------------------------------

    public function getFontSize()
    {
        return $this->fontSize;
    }

    // ------------------------------------------------------------------------

    public function getFontColor()
    {
        return $this->fontColor;
    }



    public function getString()
    {
        return $this->string;
    }

    // ------------------------------------------------------------------------

    public function getAngle()
    {
        return $this->angle;
    }
}