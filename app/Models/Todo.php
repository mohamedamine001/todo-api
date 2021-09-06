<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'description',
        'user_id',
        'finished',
        'active'
    ];

    /**
     * Belongs To Relation for Todo user
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
