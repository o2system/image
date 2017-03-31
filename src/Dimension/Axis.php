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

namespace O2System\Image\Dimension;

// ------------------------------------------------------------------------

/**
 * Class Axis
 *
 * @package O2System\Image\Dimension
 */
class Axis
{
    /**
     * Axis::$x
     *
     * Image x axis.
     *
     * @var int
     */
    protected $x = 0;

    /**
     * Axis::$y
     *
     * Image y axis.
     *
     * @var int
     */
    protected $y = 0;

    // ------------------------------------------------------------------------

    /**
     * Axis::__construct
     *
     * @param int $x Image x axis.
     * @param int $y Image y axis.
     */
    public function __construct( $x = 0, $y = 0 )
    {
        $this->x = (int)$x;
        $this->y = (int)$y;
    }

    // ------------------------------------------------------------------------

    /**
     * Axis::getX
     *
     * Gets image x axis.
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    // ------------------------------------------------------------------------

    /**
     * Axis::getY
     *
     * Gets image y axis.
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }
}