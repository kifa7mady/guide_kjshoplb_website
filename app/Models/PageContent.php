<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageContent extends Model
{
    use HasFactory, HasTranslations;
    public $translatable = ['title', 'description'];
    protected $fillable = ['title', 'description', 'page_id'];

    public function page()
    {
        return  $this->hasMany(Page::class, 'id', 'page_id');
    }
}
