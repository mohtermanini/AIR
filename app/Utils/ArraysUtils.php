<?php

namespace App\Utils;

class ArraysUtils
{
    /**
     * get each element frequency in an array
     * @return array
     */
    public static function getFrequencyArray($array)
    {
        $frequency_array = [];
        foreach ($array as $element) {
            if (!array_key_exists($element, $frequency_array)) {
                $frequency_array[$element] = 0;
            }
            $frequency_array[$element]++;
        }
        return $frequency_array;
    }

    public static function getValuesOnly($array) {
        $result = [];
        foreach($array as $key => $value) {
            array_push($result, $value);
        }
        return $result;
    }
}