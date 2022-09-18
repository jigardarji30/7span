<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class hobby extends Model
{
    use HasFactory;
    
    /**
     * relationship with user table
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

}
