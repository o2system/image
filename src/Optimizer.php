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

use ImageOptimizer\OptimizerFactory;
use O2System\Image\Optimizers\Imageoptim;
use O2System\Image\Optimizers\Optimus;

/**
 * Class Optimizer
 * @package O2System\Image
 */
class Optimizer
{
    protected $imageFactory = null;

    public function setImageFactory( $imageFactory )
    {
        if( $imageFactory instanceof Imageoptim || $imageFactory instanceof Optimus ) {
            $this->imageFactory = $imageFactory;
        }

        return $this;
    }

    public function optimize( $imageFilePath )
    {
        if( empty( $this->imageFactory ) ) {
            ( new OptimizerFactory() )->get()->optimize( $imageFilePath );
        } elseif( $this->imageFactory instanceof Imageoptim || $this->imageFactory instanceof Optimus ) {
            $imageString = $this->imageFactory->optimize( $imageFilePath, 'full' );
            file_put_contents( $imageFilePath, $imageString );
        }
    }
}