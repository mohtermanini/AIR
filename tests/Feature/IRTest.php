<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
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
        $response = $this->post("api/boolean-model/$lang", [
            "queries" => ["term1 term3"],
            "excludes" => ["term2"]
        ]);
        $content = $response->decodeResponseJson()->json();
        $true_result = ["1", "4"];
        $this->assertEqualsCanonicalizing($content, $true_result);
    }

    public function test_extendedBooleanModel_1()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $response = $this->post("api/extended-boolean-model/$lang", [
            "queries" => ["k1 k2 k3"]
        ]);
        $content = $response->decodeResponseJson()->json();
        dd($content);
        $true_result = ["1" => "0.423"];
        $this->assertEquals($content, $true_result);
    }

    public function test_extendedBooleanModel_2()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $response = $this->post("api/extended-boolean-model/$lang", [
            "queries" => ["k1 k2", "k3"]
        ]);
        $content = $response->decodeResponseJson()->json();
        $true_result = ["1" => "0.737"];
        $this->assertEquals($content, $true_result);
    }
    public function test_extendedBooleanModel_3()
    {
        $lang = "en";
        $document = ["question" => "k1", "answer" => "k3"];
        $this->post("api/document/$lang", $document);
        $response = $this->post("api/extended-boolean-model/$lang", [
            "queries" => ["k1", "k2", "k3"]
        ]);
        $content = $response->decodeResponseJson()->json();
        $true_result = ["1" => "0.816"];
        $this->assertEquals($content, $true_result);
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
        $response = $this->post("api/extended-boolean-model/$lang", [
            "queries" => ["hockey is a national mango cream cricket sport"]
        ]);
        $content = $response->decodeResponseJson()->json();
        $true_result = ["2" => "0.293",   "1" => "0.184",   "3" => "0.087"];
        $this->assertEqualsCanonicalizing($content, $true_result);
    }
}
