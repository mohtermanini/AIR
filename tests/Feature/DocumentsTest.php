<?php

namespace Tests\Feature;

use Ds\Set;
use Tests\TestCase;
use App\Models\Term;
use App\IR\TermsWeight;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use  \Illuminate\Foundation\Testing\DatabaseMigrations;

class DocumentsTest extends TestCase
{
    use DatabaseMigrations;
    public function test_termfrequency_1()
    {
        $lang = "en";
        $document = ["question" => "new york", "answer" => "times"];
        $response = $this->post("api/document/$lang", $document);
        $data = $response->decodeResponseJson()->json();
        $query = Document::with("terms")->find($data["id"]);
        $result = [];
        foreach ($query->terms as $term) {
            $result[$term->term] = $term->pivot->term_frequency;
        }
        $true_result = ["new" => "1.0", "york" => "1.0", "time" => "1.0"];
        $this->assertEquals($true_result, $result);
    }
    public function test_termfrequency_2()
    {
        $lang = "en";
        $document = ["question" => "new new", "answer" => "times"];
        $response = $this->post("api/document/$lang", $document);
        $data = $response->decodeResponseJson()->json();
        $query = Document::with("terms")->find($data["id"]);
        $result = [];
        foreach ($query->terms as $term) {
            $result[$term->term] = $term->pivot->term_frequency;
        }
        $true_result = ["new" => "1.0",  "time" => "0.5"];
        $this->assertEquals($true_result, $result);
    }

    public function test_termfrequency_3()
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
        $query_result = DB::select("select term, document_id, term_frequency from document_term");
        $tf = [];
        $documentsId = new Set();
        foreach ($query_result as $obj) {
            if (!isset($tf[$obj->term])) {
                $tf[$obj->term] = [];
            }
            $tf[$obj->term][$obj->document_id] = $obj->term_frequency;
            $documentsId->add($obj->document_id);
        }
        $true_result = [
            "ant" => ["1" => "1", "2" => "0.25"],
            "bee" => ["1" => "0.5", "2" => "0.25"],
            "cat" => ["3" => "1"],
            "dog" => ["2" => "1", "3" => "1"],
            "eel" => ["3" => "1"],
            "fox" => ["3" => "1"],
            "gnu" => ["3" => "1"],
            "hog" => ["2" => "0.25"],
        ];
        $this->assertEquals($true_result, $tf);
    }
}
