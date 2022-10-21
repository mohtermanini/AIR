<?php

namespace App\Http\Controllers;

use App\NLP\Tokenizer;
use App\NLP\Stemmer;
use App\NLP\Filter;

class TestController extends Controller
{
    public $text_en = "Are there any movement restrictions for unvaccinated travellers, in Dubai? 5:30";
    public $text_ar = "يمكن إرسال شهادة التلقيح عن طريق هذه الروابط: الخدمات الذكية من الهيئة الاتحادية للهوية والجنسية 5:30";

    public function test()
    {
        $lang = "en";
        $input_text = strtolower($this->text_en);

        $tokenizer = new Tokenizer();
        $tokenized_text = $tokenizer->tokenize($input_text);
        print_r($tokenized_text);

        echo ("<br>");

        $filter = new Filter();
        $filtered_text = $filter->remove_punctuation_marks($tokenized_text);
        print_r($filtered_text);

        echo ("<br>");

        $filtered_text = $filter->remove_stop_words($filtered_text, $lang);
        print_r($filtered_text);

        echo ("<br>");

        $stemmer = new Stemmer();
        $stemmed_text = $stemmer->stem($filtered_text, $lang);
        print_r($stemmed_text);
    }
}
