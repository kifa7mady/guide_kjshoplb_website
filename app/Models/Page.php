<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory,HasTranslations ;
    public $translatable = ['name','page_content'];
    protected $fillable = ['name', 'type','page_content'];


     // * Relations
     public function content()
     {
         return  $this->hasMany(PageContent::class, 'page_id', 'id');
     }
}
