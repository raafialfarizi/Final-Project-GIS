<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sumsel;

class SumselController extends Controller
{
    public function index()
    {
        $sumsels = Sumsel::all();
        return view('choropleth_map', compact('sumsels'));
    }
}
