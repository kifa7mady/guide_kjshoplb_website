<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
// use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory,HasTranslations;


    public $translatable = ['name'];
    protected $fillable = [
        'name',
        'email',
        'status',
        'mobile',
        'image',
        'password',
    ];
}
