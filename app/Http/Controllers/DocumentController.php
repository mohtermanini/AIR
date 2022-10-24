<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\IR\TermsWeight;
use App\Models\Document;
use App\Http\Requests\StoreDocumentRequest;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::all();
        return response($documents, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDocumentRequest $request, $lang)
    {
        $document = Document::create($request->all());
        $document_text = $request->question . " " . $request->answer;
        $terms_array = NLPController::getStemmedTermsFromText($document_text, $lang);
        TermController::storeTerms($terms_array, $document->id);
        return response($document, 201);;
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
        return response($document, 200);;
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
        return response(null, 204);
    }
}
