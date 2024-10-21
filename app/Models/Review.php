<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    use HasFactory;
    protected $fillable = ['review', 'rating'];
    //Creating relation with Base Table Book
    public function book(){

        return $this->belongsTo(Book::class);
    }

    protected static function booted()
    {
        //Whenever the Review model is modified below code will be triggered
        static::updated((fn (Review $review)=> cache()->forget('book:'. $review->book_id)));

        static::deleted(fn(Review $review) => cache()->forget('book:' . $review->book_id));
    }
}
