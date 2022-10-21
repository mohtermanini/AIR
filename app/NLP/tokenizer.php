<?php

namespace App\NLP;

use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;

class Tokenizer
{
    public function tokenize($text)
    {
        $tok = new WhitespaceAndPunctuationTokenizer();
        return $tok->tokenize($text);
    }
}
