<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\NLPController;

class NLPTest extends TestCase
{
   
    public function test_getStemmedTermsFromText_english_1()
    {
        $lang = "en";
        $text_en = "Are there any movement restrictions for unvaccinated travellers, in Dubai at 5:30";
        $stemmed_terms = NLPController::getStemmedTermsFromText($text_en, $lang);
        $true_result = ["movement", "restrict", "unvaccin", "travel", "dubai", "5", "30"];
        $this->assertEquals($stemmed_terms, $true_result);
    }

    public function test_getStemmedTermsFromText_english_2()
    {
        $lang = "en";
        $text_en = "Following arrival, what are the procedures for exiting Dubai Airports?";
        $stemmed_terms = NLPController::getStemmedTermsFromText($text_en, $lang);
        $true_result = ["follow", "arriv", "procedur", "exit", "dubai", "airport"];
        $this->assertEquals($stemmed_terms, $true_result);
    }
}
