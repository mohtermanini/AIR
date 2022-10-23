<?php

namespace App\Utils;

use Ds\Set;

class SetsUtils
{
    public static function intersectListsArray($lists)
    {
        if (empty($lists)) {
            return $lists;
        }

        usort($lists, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return (count($a) > count($b)) ? 1 : -1;
        });

        $result = $lists[0];
        for ($i = 1; $i < count($lists); $i++) {
            $result = SetsUtils::intersectLists($result, $lists[$i]);
        }
        return $result;
    }

    public static function intersectLists($list1, $list2)
    {
        $i = $j = 0;
        $result = [];
        while ($i < count($list1) && $j < count($list2)) {
            if ($list1[$i] < $list2[$j]) {
                ++$i;
            } else if ($list1[$i] > $list2[$j]) {
                ++$j;
            } else {
                array_push($result, $list1[$i]);
                ++$i;
                ++$j;
            }
        }
        return $result;
    }

    public static function differenceLists($list1, $list2)
    {
        $set = new Set($list2);
        $result = [];
        for ($i = 0; $i < count($list1); $i++) {
            if (!$set->contains($list1[$i])) {
                array_push($result, $list1[$i]);
            }
        }
        return $result;
    }

    public static function unionListsArray($lists)
    {
        if (empty($lists)) {
            return $lists;
        }
        $result = $lists[0];
        for ($i = 1; $i < count($lists); $i++) {
            $result = SetsUtils::unionLists($result, $lists[$i]);
        }
        return $result;
    }

    public static function unionLists($list1, $list2)
    {
        $i = $j = 0;
        $result = [];
        while ($i < count($list1) && $j < count($list2)) {
            if ($list1[$i] < $list2[$j]) {
                array_push($result, $list1[$i++]);
            } else if ($list1[$i] > $list2[$j]) {
                array_push($result, $list2[$j++]);
            } else {
                array_push($result, $list1[$i]);
                ++$i;
                ++$j;
            }
        }
        while ($i < count($list1)) {
            array_push($result, $list1[$i++]);
        }
        while ($j < count($list2)) {
            array_push($result, $list2[$j++]);
        }
        return $result;
    }
}
