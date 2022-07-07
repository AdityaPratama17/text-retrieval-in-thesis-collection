<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Index extends Model
{
    use HasFactory;
    // protected $fillable = ['id','id_doc','id_term','tf'];
    protected $guarded = ['id'];

    // public function documents()
    // {
    //     return $this->hasMany(Document::class, 'id_doc');
    // }

    // public function terms()
    // {
    //     return $this->hasMany(Term::class, 'id_term');
    // }
}
