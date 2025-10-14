<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expense extends Model
{
    protected $fillable = [
        'title',
        'description',
        'amount',
        'category_id',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
