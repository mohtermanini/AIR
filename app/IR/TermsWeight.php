<?php

namespace App\IR;

use Ds\Set;
use App\Models\Document;
use App\Utils\ArraysUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TermsWeight
{

    public static function getTermsFrequencyAcrossDocuments($terms = null)
    {
        $query_result = DB::table("document_term")
            ->select("term", DB::raw("count(term) as documents_count"));
        if ($terms != null) {
            $query_result =  $query_result->whereIn("term", $terms);
        }
        $query_result =  $query_result->groupBy("term")->get();
        $count = [];
        foreach ($query_result as $object) {
            $count[$object->term] = $object->documents_count;
        }
        return $count;
    }

    public static function getInverseDocumentFrequency($terms = null, $precision = null)
    {
        $tf = TermsWeight::getTermsFrequencyAcrossDocuments($terms);
        $idf = [];
        $documents_count = Document::count();
        foreach ($tf as $term => $frequency) {
            $idf[$term] = log(($documents_count / $frequency), 2);
            if (isset($precision)) {
                $idf[$term] = number_format((float) $idf[$term], $precision, '.', '') + 0;
            }
        }
        return $idf;
    }

    public static function getStoredWeights($terms, $precision = null)
    {
        $query_result = DB::table("document_term")
            ->select('term', 'document_id', 'term_frequency')
            ->whereIn("term", $terms)->get();
        $tf = [];
        $documentsId = new Set();
        foreach ($query_result as $obj) {
            if (!isset($tf[$obj->term])) {
                $tf[$obj->term] = [];
            }
            $tf[$obj->term][$obj->document_id] = $obj->term_frequency;
            $documentsId->add($obj->document_id);
        }
        $documentsId = $documentsId->toArray();
        $idf = TermsWeight::getInverseDocumentFrequency($terms, $precision);
        $weights = [];
        foreach ($documentsId as $document_id) {
            $weights[$document_id] = [];
            foreach ($terms as $term) {
                $weight =  (isset($tf[$term][$document_id]) ? $tf[$term][$document_id] : 0) * $idf[$term];
                if (isset($precision)) {
                    $weight = number_format((float) $weight, $precision, '.', '') + 0;
                }
                $weights[$document_id][$term] = $weight;
            }
        }
        return $weights;
    }

    public static function computeWeight($terms, $precision = null)
    {
        $frequency_array = ArraysUtils::getFrequencyArray($terms);
        $max_frequency = max($frequency_array);
        $idf = TermsWeight::getInverseDocumentFrequency($terms, $precision);
        $weight = [];
        foreach ($frequency_array as $term => $frequency) {
            $weight[$term] = ($frequency / $max_frequency) * (isset($idf[$term]) ? $idf[$term] : 0);
            if (isset($precision)) {
                $weight[$term] = number_format((float) $weight[$term], $precision, '.', '') + 0;
            }
        }
        return $weight;
    }

    public static function computeDocumentMagnitude($document_id, $idf) {
        $terms = Document::with("terms")->find($document_id)->terms;
        $magnitude = 0;
        foreach($terms as $obj) {
            $term_frequency = $obj->pivot->term_frequency;
            $magnitude += ($term_frequency * $idf[$obj->term]) * ($term_frequency * $idf[$obj->term]);
        }
        $magnitude = sqrt($magnitude);
        return $magnitude;
    }
}
