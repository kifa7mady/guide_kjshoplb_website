<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'talent_id',
        'street_address',
        'city',
        'postcode',
        'country_id',
        'region_id',
        'region'
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class, 'talent_id');
    }


    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
