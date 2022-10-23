<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;
use App\IR\ExtendedBooleanModel;
use App\Utils\ArraysUtils;
use App\Utils\SetsUtils;

class IRController extends Controller
{
    public function booleanModel(Request $request, $lang)
    {
        $result =  [];
        foreach ($request->queries as $query) {
            $lists = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($query, $lang));
            $lists = SetsUtils::intersectListsArray($lists);
            array_push($result, $lists);
        }
        $result = SetsUtils::unionListsArray($result);

        if (isset($request->excludes)) {
            $excludes = implode(" ", $request->excludes);
            $excludes = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($excludes, $lang));
            $excludes = SetsUtils::intersectListsArray($excludes);
            $result = SetsUtils::differenceLists($result, $excludes);
        }
        return response($result, 200);
    }

    public static function extendedBooleanModel(Request $request, $lang)
    {
        $documentsAndResults = [];
        for ($q = 0; $q < count($request->queries); $q++) {
            $query = $request->queries[$q];
            $lists = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($query, $lang));
            $documentsVectors = [];
            for ($i = 0; $i < count($lists); $i++) {
                foreach ($lists[$i] as $documentId) {
                    if (!isset($documentsVectors[$documentId])) {
                        $documentsVectors[$documentId] = array_fill(0, count($lists), 0);
                    }
                    $documentsVectors[$documentId][$i] = 1;
                }
            }
            foreach ($documentsVectors as $documentId => $documentVector) {
                if (!isset($documentsAndResults[$documentId])) {
                    $documentsAndResults[$documentId] = array_fill(0, count($request->queries), 0);
                }
                $documentsAndResults[$documentId][$q] = ExtendedBooleanModel::andQuery($documentVector);
            }
        }
        $documentsOrResults = [];
        foreach ($documentsAndResults as $documentId => $results) {
            $documentsOrResults[$documentId] = ExtendedBooleanModel::orQuery($results);
        }
        $results = $documentsOrResults;
        foreach ($results as $doucmentId => $result) {
            $results[$doucmentId] = number_format((float) $result, 3, '.', '');
        }
        if (isset($request->excludes)) {
            $excludes = implode(" ", $request->excludes);
            $excludes = ArraysUtils::getValuesOnly(IRController::getInvertedIndexFromText($excludes, $lang));
            if (!empty($excludes)) {
                foreach ($results as $documentId => $result) {
                    $results[$documentId] = (1 - $result) * (1 - $result);
                }
                foreach ($excludes as $documentList) {
                    foreach ($documentList as $documentId) {
                        $results[$documentId] += 1;
                    }
                }
                foreach ($results as $documentId => $result) {
                    $results[$documentId] = 1 - sqrt($result / (count($excludes) + 1));
                }
            }
        }
        uksort($results, function ($a, $b) use ($results) {
            if ($results[$a] == $results[$b]) {
                return $a < $b ? -1 : $a != $b;
            }
            return $results[$b] - $results[$a];
        });
        return response($results, 200);
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
}
