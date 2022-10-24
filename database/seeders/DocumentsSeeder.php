<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use App\Http\Controllers\NLPController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\DocumentController;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lang = "en";
        // $documents = [
        //     ["question" => "term1", "answer" => "term3"],
        //     ["question" => "term2", "answer" => "term4 term6"],
        //     ["question" => "term1", "answer" => "term2 term3 term4 term5"],
        //     ["question" => "term1", "answer" => "term3 term6"],
        //     ["question" => "term3", "answer" => "term4"],
        //     ["question" => "one", "answer" => "two"],
        //     ["question" => "three two", "answer" => "four"],
        //     ["question" => "one two", "answer" => "three"],
        //     ["question" => "one", "answer" => "two"],
        // ];
        // $documents = [
        //     ["question" => "k1 k2 k3 k5 k6 k7", "answer" => ""],
        //     ["question" => "k1 k2 k3 k4", "answer" => ""],
        //     ["question" => "k6 k7", "answer" => "term2 term3 term4 term5"],
        //     ["question" => "k2 k3 k6 k7", "answer" => "term2 term3 term4 term5"],
        //     ["question" => "k3 k6 k7", "answer" => "term2 term3 term4 term5"],
        //     ["question" => "k1 k3 ", "answer" => "term2 term3 term4 term5"],
        // ];
        // $documents = [
        //     ["question" => "play guitar", "answer" => "write"],
        //     ["question" => "zoo zoo", "answer" => "zooooooo"],
        //     ["question" => "playing field", "answer" => "football"]
        // ];
        // foreach ($documents as $document) {
        //     $document = Document::create(["question" => $document["question"], "answer" => $document["answer"]]);
        //     $document_text = $document["question"] . " " . $document["answer"];
        //     $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
        //     TermController::storeTerms($terms_array, $document->id);
        // }

        $json = json_decode(file_get_contents('public/json/data-en.json'));
        $lang = "en";
        foreach ($json as $el) {
            $document = Document::create([
                "question" => $el->question,
                "answer" => $el->answer
            ]);
            $document_text = $el->question . " " . $el->answer;
            $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
            TermController::storeTerms($terms_array, $document->id);
        }

        $json = json_decode(file_get_contents('public/json/data-ar.json'));
        $lang = "ar";
        foreach ($json as $el) {
            $document = Document::create([
                "question" => $el->question,
                "answer" => $el->answer
            ]);
            $document_text = $el->question . " " . $el->answer;
            $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
            TermController::storeTerms($terms_array, $document->id);
        }
    }
}
