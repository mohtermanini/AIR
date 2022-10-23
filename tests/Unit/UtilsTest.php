<?php

namespace Tests\Unit;

use App\Utils\ArraysUtils;
use PHPUnit\Framework\TestCase;
use App\Utils\SetsUtils;

class UtilsTest extends TestCase
{

    public function test_intersectLists_empty_result()
    {
        $list1 = [1, 5, 8, 10, 22, 43];
        $list2 = [3, 21, 100];
        $result = SetsUtils::intersectLists($list1, $list2);
        $this->assertEquals($result, []);
    }

    public function test_intersectLists_non_empty_result()
    {
        $list1 = [1, 5, 8, 10, 22, 43];
        $list2 = [3, 8, 22, 100];
        $result = SetsUtils::intersectLists($list1, $list2);
        $this->assertEquals($result, [8, 22]);
    }

    public function test_intersectListsArray_result_1()
    {
        $lists = [
            [1, 5, 8, 10, 22, 43],
            [1, 3, 8, 22, 100],
            [8, 12, 43],
            [1, 5, 8, 34, 100]
        ];
        $result = SetsUtils::intersectListsArray($lists);
        $this->assertEquals($result, [8]);
    }

    public function test_intersectListsArray_result_2()
    {
        $lists = [
            [1, 5, 8, 10, 22, 43],
            [1, 3, 8, 22, 100],
            [8, 12, 43],
            [1, 5, 34, 100]
        ];
        $result = SetsUtils::intersectListsArray($lists);
        $this->assertEquals($result, []);
    }


    public function test_differenceLists_empty_result()
    {
        $list1 = [5, 8, 43];
        $list2 = [1, 5, 8, 10, 22, 43];
        $result = SetsUtils::differenceLists($list1, $list2);
        $this->assertEquals($result, []);
    }

    public function test_differenceLists_non_empty_result()
    {
        $list1 = [1, 5, 8, 10, 22, 43];
        $list2 = [3, 8, 22, 100];
        $result = SetsUtils::differenceLists($list1, $list2);
        $this->assertEquals($result, [1, 5, 10, 43]);
    }

    public function test_unionLists_empty_result()
    {
        $list1 = [];
        $list2 = [];
        $result = SetsUtils::unionLists($list1, $list2);
        $this->assertEquals($result, []);
    }

    public function test_unionLists_non_empty_result()
    {
        $list1 = [5, 8, 43];
        $list2 = [1, 5, 8, 10, 22, 43];
        $result = SetsUtils::unionLists($list1, $list2);
        $this->assertEquals($result, [1, 5, 8, 10, 22, 43]);
    }

    public function test_unionListsArray_result_1()
    {
        $lists = [
            [1, 5, 8, 10, 22, 43],
            [1, 3, 8, 22, 100],
            [8, 12, 43],
            [1, 5, 34, 100]
        ];
        $result = SetsUtils::unionListsArray($lists);
        $true_result = [1, 3, 5, 8, 10, 12, 22, 34, 43, 100];
        $this->assertEquals($result, $true_result);
    }

    public function test_getFrequencyArray()
    {
        $tokens = ["hello", "hello", "hi", "hello", "red", "red"];
        $result = ArraysUtils::getFrequencyArray($tokens);
        $true_result = ["hello" => 3, "hi" => 1, "red" => 2];
        $this->assertEqualsCanonicalizing($result, $true_result);
    }
}
