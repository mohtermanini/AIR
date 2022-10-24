<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['question', 'answer'];

    public function terms()
    {
        return $this->belongsToMany("App\Models\Term", "document_term", "document_id", "term")
            ->withPivot("frequency", "term_frequency");
    }
}
