<?php

namespace App\NLP;

use Exception;
use Wamania\Snowball\StemmerFactory;
use Nouralhadi\Stemmer\Http\Helpers\ISRIStemmer;

/*LancasterStemmer
    use NlpTools\Stemmers\LancasterStemmer;
    $stm = new LancasterStemmer();
    return $stm->stemAll($text);
*/



class Stemmer
{
    public function stem($text, $lang)
    {
        $stemmer = null;
        if ($lang == "en") {
            $stemmer = StemmerFactory::create('en');
        } else if ($lang == "ar") {
            $stemmer = new ISRIStemmer();
        } else {
            throw new Exception("Illegal Argument Exception");
        }
        $stemmed_text = [];
        foreach ($text as $word) {
            array_push($stemmed_text, $stemmer->stem($word));
        }
        return $stemmed_text;
    }
}
