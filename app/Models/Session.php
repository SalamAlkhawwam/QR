<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $table = "sessionss";
    protected $primaryKey = "id";
    public $timestamp = true ;
    public $fillable = [
        'password',
        'date',
        'startSession',
        'closeSession'
    ];
}
