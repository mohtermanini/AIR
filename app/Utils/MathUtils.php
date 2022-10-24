<?php

namespace App\Utils;

class MathUtils
{
    public static function computeVectorMagnitude($vector)
    {
        $magnitude = 0;
        foreach ($vector as $dimension) {
            $magnitude += $dimension * $dimension;
        }
        $magnitude = sqrt($magnitude);
        return $magnitude;
    }

}
