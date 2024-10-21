<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//Book can have many reviews but each record in Review table can only be associted with one record of book table
class Book extends Model
{

    use HasFactory;

    //Creating relationship with review table
    public function reviews(){

        return $this->hasMany(Review::class);
    }

    //Local query scope

    public function scopeTitle(Builder $query, string $title):Builder{

        return $query->where('title', 'LIKE', '%' . $title . '%');

    }

    // public function scopePopular(Builder $query, $from =null, $to = null):Builder | QueryBulder{

    //     return $query->withCount('reviews')->orderBy('reviews_count', 'desc');
    // }

    // public function scopePopular(Builder $q, $from = null, $to = null):Builder | QueryBulder{

    //     return $q->withCount([
    //         'reviews' => function(Builder $q) use ($from, $to){
    //             if($from && !$to){

    //                 $q->where('created_at', '>', $from);

    //             }elseif(!$from && $to){

    //                 $q->where('created_at', '<', $to);

    //             }elseif($from && $to){
    //                 $q->whereBetween('created_at',[$from, $to]);
    //             }
    //         }
    //     ]);
    // }

    //OR

    //From command promt
    //\App\Models\Book::popular('2023-01-01', '2023-03-30')->get();
    public function scopePopular(Builder $query, $from = null, $to = null):Builder | QueryBuilder{

        // return $query->withCount('reviews')
        // ->orderBy('reviews_count', 'desc');

        return $query->withCount([
            //arrow function filter those reviews further
            //If we use full name like function instead of fn then we have to use
            // `use` to access $from and $to from parent function
            //this is clouser.

            //There is limitation of arrow function, that you can only have one  line expression and you need not to use `use` to access the parent variables like parameters.
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])->orderBy('reviews_count','desc');
    }


    public function scopeHighestRated(Builder $query, $from = null, $to = null):Builder | QueryBuilder{

        // return $query->withAvg('reviews', 'rating')
        // ->orderBy('reviews_avg_rating', 'desc');

        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ],'rating')->orderBy('reviews_avg_rating', 'desc');
    }


    private function dateRangeFilter(Builder $query, $from = null, $to = null){

        if($from && !$to){

            $query->where('created_at', '>=', $from)->orderBy('created_at', 'desc');

        }elseif(!$from && $to){

            $query->where('created_at', '<=', $to)->orderBy('created_at', 'desc');

        }elseif($from && $to){
            $query->whereBetween('created_at',[$from, $to])->orderBy('created_at', 'desc');
        }
    }

    public function scopeMinReviews(Builder $query, int $minReviews):Builder | QueryBuilder{

        return $query->having('reviews_count', '>=' , $minReviews);
    }

    public function scopePopularLastMonth(Builder $query):Builder|QueryBuilder{

       return $query->popular(now()->subMonth(), now())
       ->highestRated(now()->subMonth(), now())
       ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query):Builder|QueryBuilder{

        return $query->popular(now()->subMonths(6), now())
        ->highestRated(now()->subMonths(6), now())
        ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query):Builder|QueryBuilder{

        return $query->highestRated(now()->subMonth(), now())
        ->popular(now()->subMonth(), now())
        ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query):Builder|QueryBuilder{

        return $query->highestRated(now()->subMonths(6), now())
        ->popular(now()->subMonths(6), now())
        ->minReviews(5);
    }

}
