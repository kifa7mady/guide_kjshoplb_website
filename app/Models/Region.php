<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    use HasFactory, HasTranslations;

    protected $fillable = ['name','country_id','path'];
    public $translatable = ['name'];

    public function getSeoUrlAttribute(){
        return url('/guide/region/' . (Str::slug($this->getTranslation('name', 'en')) ?: 'All') . '/' . ($this->id ?: 0 ) . '/'  );
    }


    public function customerJobs()
    {
        return $this->hasMany(CustomerJob::class)->orderBy('created_at', 'asc');
    }

}
