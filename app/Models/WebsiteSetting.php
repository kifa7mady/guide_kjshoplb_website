<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteSetting extends Model
{
    use HasFactory,HasTranslations;
    protected $fillable = [
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'tik_tok',
        'latitude',
        'longitude',
        'building',
        'address',
        'open_day',
        'close_day',
        'open_time',
        'close_time',
        'main_email',
        'second_email',
        'client_mobile',
        'talent_mobile',
    ];

    public $translatable = ['address','open_day','close_day'];

    
    protected function ClientMobile(): Attribute

    {

        return Attribute::make(

            get: fn ($value) => json_decode($value, true),

            set: fn ($value) => json_encode($value),

        );

    }

    protected function TalentMobile(): Attribute

    {

        return Attribute::make(

            get: fn ($value) => json_decode($value, true),

            set: fn ($value) => json_encode($value),

        );

    }
}
