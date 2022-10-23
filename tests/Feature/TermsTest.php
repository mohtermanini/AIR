<?php

namespace Tests\Feature;

use App\Http\Controllers\IRController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use  \Illuminate\Foundation\Testing\DatabaseMigrations;

class TermsTest extends TestCase
{
    use DatabaseMigrations;
    public function test_getInvertedIndexFromText_en_1()
    {
        $lang = "en";
        $documents = [
            ["question" => "term1", "answer" => "term3"],
            ["question" => "term2", "answer" => "term4 term6"],
            ["question" => "term1", "answer" => "term2 term3 term4 term5"],
            ["question" => "term1", "answer" => "term3 term6"],
            ["question" => "term3", "answer" => "term4"]
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }
        $map = IRController::getInvertedIndexFromText("term1 term3 term2", $lang);
        $true_result = ["term1" => [1, 3, 4], "term2" => [2, 3], "term3" => [1, 3, 4, 5]];
        $this->assertEquals($map, $true_result);
    }

    public function test_getInvertedIndexFromText_en_2()
    {
        $lang = "en";
        $documents = [
            ["question" => "one", "answer" => "two"],
            ["question" => "three two", "answer" => "four"],
            ["question" => "one two", "answer" => "three"],
            ["question" => "one", "answer" => "two"]
        ];
        foreach ($documents as $document) {
             $this->post("api/document/$lang", $document);
        }
        $map = IRController::getInvertedIndexFromText("one two three four", $lang);
        $true_result = [
            "one" => [1, 3, 4], "two" => [1, 2, 3, 4], "three" => [2, 3], "four" => [2]
        ];
        $this->assertEquals($map, $true_result);
    }

    public function test_getInvertedIndexFromText_en_3()
    {
        $lang = "en";
        $documents = [
            ["question" => "one", "answer" => "two"],
            ["question" => "three two", "answer" => "four"],
            ["question" => "one two", "answer" => "three"],
            ["question" => "one", "answer" => "two"]
        ];
        foreach ($documents as $document) {
             $this->post("api/document/$lang", $document);
        }
        $map = IRController::getInvertedIndexFromText("one five six", $lang);
        $true_result = [
            "one" => [1, 3, 4], "five" => [], "six" => []
        ];
        $this->assertEquals($map, $true_result);
    }
}
