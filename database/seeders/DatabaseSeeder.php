<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Book::factory(33)->create()->each(function($book){

            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
            ->good()
            ->for($book) //set the book id column which is left empty in Factory
            ->create();

        });
        Book::factory(33)->create()->each(function($book){

            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
            ->average()
            ->for($book) //set the book id column which is left empty in Factory
            ->create();
        });
        Book::factory(33)->create()->each(function($book){

            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
            ->bad()
            ->for($book) //set the book id column which is left empty in Factory
            ->create();

        });
    }
}
