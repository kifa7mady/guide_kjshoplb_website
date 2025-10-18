<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory,HasTranslations;
    protected $fillable = ['name','iso_code','phone_codes'];
    public $translatable = ['name'];
}
