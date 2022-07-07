<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'image',
    ];

    function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    function category() {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
