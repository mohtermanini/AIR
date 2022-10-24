<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $primaryKey = "term";
    protected $keyType = "string";
    public $incrementing = false;
    
    protected $fillable = ['term'];
    protected $hidden = ["created_at", "updated_at"];

    public function documents() {
        return $this->belongsToMany("App\Models\Document","document_term","term","document_id")
            ->orderBy("document_id")->select("id")->withPivot("frequency", "term_frequency");
    }

}
