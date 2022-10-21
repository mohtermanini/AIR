<?php

namespace App\NLP;

use Exception;
use NlpTools\Stemmers\LancasterStemmer;
use Nouralhadi\Stemmer\Http\Helpers\ISRIStemmer;



class Stemmer
{
    public function stem($text, $lang)
    {
        $stm = null;
        if ($lang == "en") {
            $stm = new LancasterStemmer();
            return $stm->stemAll($text);
        } else if ($lang == "ar") {
            $stm = new ISRIStemmer();
            $stemmed_text = [];
            foreach ($text as $word) {
                array_push($stemmed_text, $stm->stem($word));
            }
            return $stemmed_text;
        }

        throw new Exception("Illegal Argument Exception");
    }
}
