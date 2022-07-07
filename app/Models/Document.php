<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['id','doc','judul'];
    // protected $guarded = ['id'];

    // public function indexs()
    // {
    //     return $this->hasMany(Index::class);
    // }
}
