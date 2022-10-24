<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\IR\TermsWeight;
use App\IR\VectorModel;
use App\Models\Document;
use App\Utils\SetsUtils;
use App\Utils\ArraysUtils;
use Illuminate\Http\Request;
use App\IR\ExtendedBooleanModel;

class IRController extends Controller
{
    public static function booleanModel($queries, $excludes, $lang)
    {
        $result =  [];
        foreach ($queries as $query) {
            $lists = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($query, $lang));
            $lists = SetsUtils::intersectListsArray($lists);
            array_push($result, $lists);
        }
        $result = SetsUtils::unionListsArray($result);

        if (isset($excludes)) {
            $excludes = implode(" ", $excludes);
            $excludes = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($excludes, $lang));
            $excludes = SetsUtils::unionListsArray($excludes);
            $result = SetsUtils::differenceLists($result, $excludes);
        }
        $documents = Document::whereIn("id", $result)->get();
        return $documents;
    }

    public static function extendedBooleanModel($queries, $excludes, $lang)
    {
        $documentsAndResults = [];
        for ($q = 0; $q < count($queries); $q++) {
            $query = $queries[$q];
            $lists = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($query, $lang));
            $documentsVectors = [];
            for ($i = 0; $i < count($lists); $i++) {
                foreach ($lists[$i] as $document_id) {
                    if (!isset($documentsVectors[$document_id])) {
                        $documentsVectors[$document_id] = array_fill(0, count($lists), 0);
                    }
                    $documentsVectors[$document_id][$i] = 1;
                }
            }
            foreach ($documentsVectors as $document_id => $documentVector) {
                if (!isset($documentsAndResults[$document_id])) {
                    $documentsAndResults[$document_id] = array_fill(0, count($queries), 0);
                }
                $documentsAndResults[$document_id][$q] = ExtendedBooleanModel::andQuery($documentVector);
            }
        }
        $documentsOrResults = [];
        foreach ($documentsAndResults as $document_id => $results) {
            $documentsOrResults[$document_id] = ExtendedBooleanModel::orQuery($results);
        }
        $results = $documentsOrResults;
        foreach ($results as $doucmentId => $result) {
            $results[$doucmentId] = number_format((float) $result, 3, '.', '') + 0;
        }
        if (isset($excludes)) {
            $excludes = implode(" ", $excludes);
            $excludes = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($excludes, $lang));
            if (!empty($excludes)) {
                foreach ($results as $document_id => $result) {
                    $results[$document_id] = (1 - $result) * (1 - $result);
                }
                foreach ($excludes as $documentList) {
                    foreach ($documentList as $document_id) {
                        $results[$document_id] += 1;
                    }
                }
                foreach ($results as $document_id => $result) {
                    $results[$document_id] = 1 - sqrt($result / (count($excludes) + 1));
                }
            }
        }
        $ids = array_keys($results);
        $documents = Document::whereIn("id", $ids)->get();
        foreach ($documents as $document) {
            $document->rank = $results[$document->id];
        }
        $documents = $documents->sortByDesc(function ($document) {
            return $document->rank;
        });
        return $documents;
    }

    public static function getInvertedIndexFromText($text, $lang)
    {
        $stemmed_terms = NLPController::getStemmedTermsFromText($text, $lang);
        $terms = Term::whereIn("term", $stemmed_terms)->with("documents")->get();
        $lists = [];
        foreach ($stemmed_terms as $stemmed_term) {
            $lists[$stemmed_term] = [];
        }
        foreach ($terms as $object) {
            foreach ($object->documents as $document) {
                array_push($lists[$object->term], $document->id);
            }
        }
        return $lists;
    }

    public static function vectorModel($queries, $lang)
    {
        $input_text = $queries[0];
        $terms = NLPController::getStemmedTermsFromText($input_text, $lang);
        $query_weights = TermsWeight::computeWeight($terms);
        $documents_weights = TermsWeight::getStoredWeights($terms);
        $results = [];
        foreach ($documents_weights as $document_id => $terms_weights) {
            $results[$document_id] = VectorModel::computeSimilarity(
                $terms_weights,
                $query_weights,
                $document_id,
                TermsWeight::getInverseDocumentFrequency()
            );
        }
        $ids = array_keys($results);
        $documents = Document::whereIn("id", $ids)->get();
        foreach ($documents as $document) {
            $document->rank = $results[$document->id];
        }
        $documents = $documents->sortByDesc(function ($document) {
            return $document->rank;
        });
        return $documents;
    }
}
