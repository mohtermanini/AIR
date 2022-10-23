<?php

namespace App\Http\Controllers;

use App\NLP\Filter;
use App\NLP\Stemmer;
use App\NLP\Tokenizer;

class NLPController extends Controller
{
     /**
     * return array of stemmed words from the text after filtering it
     * @return array
     */
    public static function getStemmedTermsFromText($text, $lang)
    {
        $text = strtolower($text);

        $tokenizer = new Tokenizer();
        $tokenized_text = $tokenizer->tokenize($text);

        $filter = new Filter();
        $filtered_text = $filter->remove_punctuation_marks($tokenized_text);
        $filtered_text = $filter->remove_stop_words($filtered_text, $lang);

        $stemmer = new Stemmer();
        $stemmed_text = $stemmer->stem($filtered_text, $lang);

        return $stemmed_text;
    }

    
}
