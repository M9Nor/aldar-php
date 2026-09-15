<?php

namespace App\Glide;

use League\Glide\Api\Encoder as BaseEncoder;

/**
 * Glide 1.5 quality parity: the Glide 3 encoder with Glide 1.5's default quality of 90.
 *
 * Glide 3's Encoder::getQuality() falls back to 85 when no valid `q` is given, where Glide 1.5's
 * Encode manipulator fell back to 90. Resized images always pass `q` through the server defaults,
 * but `/img/original` clears the defaults, so without this class newly generated original JPEGs
 * would be encoded at 85. ImageServiceProvider passes this class as the server's `encoder`. The
 * default only applies at encode time: params and cache paths are unchanged.
 *
 * getQuality() is the Glide 3.2.0 body with the default changed from 85 to 90; an explicit `q`
 * is handled exactly as before.
 */
class Encoder extends BaseEncoder
{
    /**
     * Resolve quality.
     *
     * @return int The resolved quality.
     */
    public function getQuality(): int
    {
        $default = 90;
        $q = $this->getParam('q');

        if (
            !is_numeric($q)
            || $q < 0
            || $q > 100
        ) {
            return $default;
        }

        return (int) $q;
    }
}
