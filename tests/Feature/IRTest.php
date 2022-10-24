<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\IR\TermsWeight;
use App\Models\Document;
use App\Http\Controllers\IRController;
use App\Http\Controllers\NLPController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use  \Illuminate\Foundation\Testing\DatabaseMigrations;

class IRTest extends TestCase
{
    use DatabaseMigrations;
    public function test_booleanModel_1()
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
        $content = json_encode(IRController::booleanModel(["term1 term3"], ["term2"], $lang));
        
        $true_result = json_encode(Document::whereIn("id", ["1", "4"])->get());
        $this->assertEquals($true_result, $content);
    }

    public function test_extendedBooleanModel_1()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $content = json_encode(IRController::extendedBooleanModel(["k1 k2 k3"], [], $lang));

        $true_result = Document::where("id", 1)->get();
        $true_result[0]->rank = 0.423;
        $true_result = json_encode($true_result);
        $this->assertEquals($true_result, $content);
    }

    public function test_extendedBooleanModel_2()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $content = json_encode(IRController::extendedBooleanModel(["k1 k2", "k3"], [], $lang));
        $true_result = Document::where("id", 1)->get();
        $true_result[0]->rank = 0.737;
        $true_result = json_encode($true_result);
        $this->assertEquals($true_result, $content);
    }
    public function test_extendedBooleanModel_3()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $content = json_encode(IRController::extendedBooleanModel(["k1", "k2", "k3"], [], $lang));

        $true_result = Document::where("id", 1)->get();
        $true_result[0]->rank = 0.816;
        $true_result = json_encode($true_result);
        $this->assertEquals($true_result, $content);
    }

    public function test_extendedBooleanModel_4()
    {
        $lang = "en";
        $documents = [
            ["question" => "ice cream", "answer" => "mango litchi"],
            ["question" => "hockey", "answer" => "cricket sport"],
            ["question" => "litchi", "answer" => "mango chocolate"],
            ["question" => "nice", "answer" => "good cute"]
        ];
        foreach ($documents as $document) {
            $this->post("api/document/$lang", $document);
        }

        $content = json_encode(IRController::extendedBooleanModel([
            "hockey is a national mango cream cricket sport"
        ], [], $lang));

        $true_result = Document::whereIn("id", [1, 2, 3])->get();
        foreach ($true_result as $obj) {
            if ($obj->id == 1) $obj->rank = 0.184;
            else if ($obj->id == 2) $obj->rank = 0.293;
            else if ($obj->id == 3) $obj->rank = 0.087;
        }
        $true_result = $true_result->sortByDesc(function ($document) {
            return $document->rank;
        });
        $true_result = json_encode($true_result);
        $this->assertEquals($true_result, $content);
    }

    public function test_vectormodel_1()
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
        $query = "ant dog";
        $content = json_encode(IRController::vectorModel([$query], $lang));
        $true_result = Document::whereIn("id", [1, 2, 3])->get();
        foreach ($true_result as $obj) {
            if ($obj->id == 1) $obj->rank = 0.6324555320336759;
            else if ($obj->id == 2) $obj->rank = 0.7023268368563554;
            else if ($obj->id == 3) $obj->rank = 0.12831948188497178;
        }
        $true_result = $true_result->sortByDesc(function ($document) {
            return $document->rank;
        });
        $true_result = json_encode($true_result);
        $this->assertEquals($true_result, $content);
    }
}
