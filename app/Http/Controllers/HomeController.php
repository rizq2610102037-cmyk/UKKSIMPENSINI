<?php

namespace App\Http\Controllers;

use App\Models\TipeKamar;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $tipeKamar = TipeKamar::all();
        return view('index', compact('tipeKamar'));
    }
}
