<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        return view("home", [
            "page_active" => "home",
            "algorithm" => "boolean-model",
            "lang" => "en",
            "prev_queries" => [],
            "prev_excludes" => []
        ]);
    }

   
 
    public function about()
    {
        return view("about", [
            "page_active" => "about"
        ]);
    }
}
