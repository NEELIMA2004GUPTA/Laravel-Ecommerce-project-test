<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewMedia extends Model
{
    protected $fillable=['review_id','type','path','mime','size'];

    public function review(){
        return $this->belongsTo(Review::class);
    }
}
