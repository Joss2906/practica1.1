<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;
    protected $table = 'verification_codes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 
        'code'
    ];
}
