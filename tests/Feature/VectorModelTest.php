<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Term;
use App\IR\TermsWeight;
use App\Http\Controllers\NLPController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use  \Illuminate\Foundation\Testing\DatabaseMigrations;

class VectorModelTest extends TestCase
{
    use DatabaseMigrations;
    public function test_computeWeight_1()
    {
        $lang = "en";
        $documents = [
            ["question" => "new", "answer" => "york times"],
            ["question" => "new", "answer" => "york post"],
            ["question" => "los angelas", "answer" => "times"]
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }
        $query = "new new times";
        $terms = NLPController::getStemmedTermsFromText($query, $lang);
        $query_weights = TermsWeight::computeWeight($terms, 2);
        $true_result = ["new" => "0.58", "time" => "0.29"];
        $this->assertEquals($true_result, $query_weights);
    }

    public function test_getInverseDocumentFrequency_1()
    {
        $lang = "en";
        $documents = [
            ["question" => "new", "answer" => "york times"],
            ["question" => "new", "answer" => "york post"],
            ["question" => "los angelas", "answer" => "times"]
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }
        $terms = Term::get()->toArray();
        $result = TermsWeight::getInverseDocumentFrequency($terms, 1);
        $true_result = ["new" => 0.6,  "time" => 0.6, "york" => 0.6, "post" => 1.6, "los" => 1.6, "angela" => 1.6];
        $this->assertEquals($true_result, $result);
    }

    public function test_getInverseDocumentFrequency_2()
    {
        $lang = "en";
        $documents = [
            ["question" => "one", "answer" => "two"],
            ["question" => "three two", "answer" => "four"],
            ["question" => "one two", "answer" => "three"],
            ["question" => "one", "answer" => "two"],
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }
        $idf = TermsWeight::getInverseDocumentFrequency(["one", "two", "three", "four"], 3);
        $true_result = ["one" => "0.415", "two" => "0.0", "three" => "1.0", "four" => "2.0"];
        $this->assertEquals($true_result, $idf);
    }

    public function test_getInverseDocumentFrequency_3()
    {
        $lang = "en";
        $documents = [
            ["question" => "ant ant", "answer" => "bee"],
            ["question" => "dog bee dog", "answer" => "hog dog ant dog"],
            ["question" => "cat", "answer" => "gnu dog eel fox"],
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }
        $idf = TermsWeight::getInverseDocumentFrequency(["ant", "bee", "cat", "dog", "eel", "fox", "gnu", "hog"], 2);
        $true_result = [
            "ant" => "0.58", "bee" => "0.58", "cat" => "1.58", "dog" => "0.58", "eel" => "1.58", "fox" => "1.58", "gnu" => "1.58", "hog" => "1.58"
        ];
        $this->assertEquals($true_result, $idf);
    }
}
