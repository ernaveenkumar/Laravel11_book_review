<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Book can have many reviews but each record in Review table can only be associted with one record of book table
class Book extends Model
{

    use HasFactory;

    //Creating relationship with review table
    public function reviews(){

        return $this->hasMany(Review::class);
    }
}
