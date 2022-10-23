<?php

namespace App\IR;


class ExtendedBooleanModel
{
    public static function andQuery($documentVector)
    {
        $nurmerator = 0;
        for ($i = 0; $i < count($documentVector); $i++) {
            $nurmerator += (1 - $documentVector[$i]) * (1 - $documentVector[$i]);
        }
        return (1 - sqrt(1.0 * $nurmerator / count($documentVector)));
    }

    public static function orQuery($documentVector)
    {
        $nurmerator = 0;
        for ($i = 0; $i < count($documentVector); $i++) {
            $nurmerator += $documentVector[$i] * $documentVector[$i];
        }
        return (sqrt(1.0 * $nurmerator / count($documentVector)));
    }
}
