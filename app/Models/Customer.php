<?php

namespace App\Models;

use Cassandra\Custom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'customer_name' => 'array', // Cast 'customer_name' as an array
        'mobile' => 'array', // Cast 'customer_name' as an array
    ];


    public function getCustomerNamesAttribute()
    {
        return implode(' & ', $this->customer_name);
    }

    public function getMobilesAttribute()
    {
        if(!empty($this->mobile)){
            return implode(' & ', $this->mobile);
        }else{
            return '';
        }
    }

    public function getCustomerCategoriesNameAttribute()
    {
        $customer_categories_name = '';
        foreach($this->categories as $category) {
            $customer_categories_name .= $category->name . ', ';
        }

        if (strlen($customer_categories_name) > 2) {
            $customer_categories_name = substr($customer_categories_name, 0, -2);
        }

        return $customer_categories_name;

    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'customer_categories', 'customer_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(CustomerImage::class);
    }

    public function getSeoUrlAttribute(){
        return '/guide/'.  $this->id . '/' . Str::slug($this->customer_names);;
    }


    public function customerjobs()
    {
        return $this->hasMany(CustomerJob::class);
    }
}
