<?php

namespace App\IR;

use App\Utils\MathUtils;

class VectorModel
{
    public static function computeSimilarity($weights1, $weights2, $document_id, $idf)
    {
        $similarity = 0;
        foreach($weights1 as $term => $weight) {
            $similarity += $weight * $weights2[$term];
        }
        $magnitude1 = TermsWeight::computeDocumentMagnitude($document_id, $idf);
        $magnitude2 = MathUtils::computeVectorMagnitude($weights2);
        $magnitude_product = $magnitude1 * $magnitude2;
        if ($magnitude_product == 0) {
            return 0;
        }
        $similarity /= $magnitude_product;
        return $similarity;
    }
}
