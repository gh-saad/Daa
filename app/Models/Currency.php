<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $table = 'currency';
    protected $primaryKey = 'code'; // Set 'code' as the primary key
    public $incrementing = false;   // Disable auto-incrementing (since 'code' is not an integer)
    protected $keyType = 'string'; 

    public $timestamps = false;     // Disable automatic timestamps

}
