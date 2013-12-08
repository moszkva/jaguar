<?php

/*
 * This file is part of the Jaguar package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jaguar\Action\Overlay;

use Jaguar\Action\Color\Brightness;
use Jaguar\Action\Color\Contrast;
use Jaguar\Action\Color\Grayscale;
use Jaguar\Action\Overlay;

class Monopin extends AbstractOverlay
{

    /**
     * {@inheritdoc}
     *
     * this effect was inspired from Marc Hibbins (http://marchibbins.com/dev/gd)
     */
    protected function doApply(\Jaguar\CanvasInterface $canvas)
    {
        $actions = array(
            new Grayscale(),
            new Brightness(-15),
            new Contrast(15),
            new Overlay(
                    $this->getOverlayCanvas('vignette.png'), 100
            )
        );

        foreach ($actions as $action) {
            $action->apply($canvas);
        }
    }

}
