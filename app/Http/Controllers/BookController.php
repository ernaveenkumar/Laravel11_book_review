<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //Request object auttmatically provides everthing from requested
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when(
            $title, // when title is not empty or null it is limiting the data to title otherwise it is not limiting the data | We are using our localQuery Scope title
            fn($query, $title) => $query ->title($title)
        );

        //match is similar to switch the only difference is it can return value
        $books = match($filter){
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()
        };


        //$books = $books->get();

        //Using Memechached
        //define keys to track changes
        $cachekey = 'books:'. $filter . ':'. $title;
        $books = cache()->remember($cachekey, 3600, fn() => $books->get());

        return view('books.index', ['books' => $books]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    //public function show(string $id)
    //Use Route Model Binding pass model name, when we pass model name laravel knows that we are looking of item in model with passed id.
   public function show(Book $book)
    {
        //
        $cacheKey = 'book:' . $book->id;

        $book = cache()->remember($cacheKey, 3600, fn() => $book->load([
                    'reviews' => fn($query) => $query->latest() //Model that is already loaded
        ]));

        return view(
            'books.show',
            [
                'book' => $book
            ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
