<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(){
        $juegos = Juego::with('multimedias')->take(10)->get();
        return view('welcome', compact('juegos'));
    }
}
