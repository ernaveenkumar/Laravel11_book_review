<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Resource Controller
//php artisan make:controller BookController --resource
//Route -> Resource -> and Classname
Route::resource('books', BookController::class);
