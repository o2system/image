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

namespace O2System\Image\Datastructures;

// ------------------------------------------------------------------------

/**
 * Class Config
 *
 * @package O2System\Parser\Metadata
 */
class Config extends \O2System\Kernel\Registries\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct( array $config = [] )
    {
        $defaultConfig = [
            'driver'              => 'gmagick',
            'maintainAspectRatio' => true,
            'focus'               => 'CENTER',
            'orientation'         => 'AUTO',
            'quality'             => 100,
            'cached'              => false,
        ];

        $config = array_merge( $defaultConfig, $config );

        if ( $config[ 'driver' ] === 'imagick' ) {
            $config[ 'driver' ] = 'imagemagick';
        } elseif ( $config[ 'driver' ] === 'gmagick' ) {
            $config[ 'driver' ] = 'graphicsmagick';
        }

        parent::__construct( $config );
    }
}