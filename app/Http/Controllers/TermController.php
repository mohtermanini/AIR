<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Document;
use App\Utils\ArraysUtils;

class TermController extends Controller
{
    public static function storeTerms($terms_array, $document_id)
    {
        $document = Document::find($document_id);
        $frequency_array = ArraysUtils::getFrequencyArray($terms_array);
        $attached_data = [];
        $terms_arr = [];
        foreach ($frequency_array as $key => $value) {
            $attached_data[$key] = ['frequency' => $value];
            array_push($terms_arr, ["term" => $key]);
        }
        Term::insertOrIgnore($terms_arr);
        $document->terms()->attach($attached_data);
    }

   
}
