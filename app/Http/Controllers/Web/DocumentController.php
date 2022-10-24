<?php

namespace App\Http\Controllers\Web;

use App\Models\Term;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IRController;
use App\Http\Controllers\NLPController;
use App\Http\Controllers\TermController;
use App\Http\Requests\StoreDocumentRequest;
use App\NLP\Tokenizer;
use Ds\Set;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function create()
    {
        return view("write", [
            "page_active" => "write"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDocumentRequest $request)
    {
        $lang = $request->lang;
        $document = Document::create([
            "question" => $request->question,
            "answer" => $request->answer
        ]);
        $document_text = $request->question . " " . $request->answer;
        $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
        TermController::storeTerms($terms_array, $document->id);
        session()->flash("success", "Question added successfully");
        return redirect()->back();
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDocumentRequest $request, $id, $lang)
    {
        $document = Document::find($id);
        $document->update($request->all());
        $document->terms()->detach();
        Term::doesntHave("documents")->delete();
        $document_text = $request->question . " " . $request->answer;
        $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
        TermController::storeTerms($terms_array, $id);
        session()->flash("success", "Question updated successfully");
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Document::find($id)->delete();
        Term::doesntHave("documents")->delete();
        session()->flash("success", "Question deleted successfully");
        return redirect()->back();
    }

    public function search()
    {
        $results = [];
        $lang = request()->lang;
        switch (request()->algorithm) {
            case "boolean-model":
                $results = IRController::booleanModel(request()->queries, request()->excludes, $lang);
                break;
            case "extended-boolean-model":
                $results = IRController::extendedBooleanModel(request()->queries, request()->excludes, $lang);
                break;
            case "vector-modal":
                $results = IRController::vectorModel(request()->queries, $lang);
        }

        $stemmed_queries = new Set();
        foreach (request()->queries as $query) {
            $query = NLPController::getStemmedTermsFromText($query, $lang);
            foreach ($query as $term) {
                $stemmed_queries->add($term);
            }
        }
        $stemmed_excludes = new Set();
        if (isset(request()->excludes)) {
            foreach (request()->excludes as $query) {
                $query = NLPController::getStemmedTermsFromText($query, $lang);
                foreach ($query as $term) {
                    $stemmed_excludes->add($term);
                }
            }
        }
        $tokenizer = new Tokenizer();
        foreach ($results as $result) {
            $this->markDocument($result, $stemmed_queries, $stemmed_excludes, $lang);
            $result->tokenized_question = $tokenizer->tokenize($result->question);
            $result->tokenized_answer = $tokenizer->tokenize($result->answer);
        }

        return view("home", [
            "results" => $results,
            "page_active" => "home",
            "algorithm" => request()->algorithm,
            "lang" => $lang,
            "prev_queries" => request()->queries,
            "prev_excludes" => isset(request()->excludes) ? request()->excludes : []
        ]);
    }

    private function markDocument($document, $queries, $excludes, $lang)
    {
        $tokenizer = new Tokenizer();
        $input_text = $document->question . " " . $document->answer;
        $tokenized_document = $tokenizer->tokenize($input_text);
        $marked_green = new Set();
        $marked_red = new Set();
        foreach ($tokenized_document as $token) {
            $stemmed_token = NLPController::getStemmedTermsFromText($token, $lang);
            if (empty($stemmed_token)) {
                continue;
            }
            if (!is_array($stemmed_token)) {
                $stemmed_token = [$stemmed_token];
            }
            foreach ($stemmed_token as $stk) {
                if ($queries->contains($stk)) {
                    $marked_green->add($token);
                    break;
                }
                if ($excludes->contains($stk)) {
                    $marked_red->add($token);
                    break;
                }
            }
        }
        $document->marked_green = $marked_green;
        $document->marked_red = $marked_red;
    }
}
