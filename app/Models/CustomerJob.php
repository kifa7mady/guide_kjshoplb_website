<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerJob extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($customerJob) {
            if (empty($customerJob->permalink)) {
                $customerJob->permalink = Str::slug($customerJob->name);
            }
        });
    }

    public function images()
    {
        return $this->hasMany(CustomerJobImage::class)->orderBy('created_at', 'asc');;
    }

    public function firstImage()
    {
        return $this->hasOne(CustomerJobImage::class)->oldest('id'); // or ->latest() depending on your needs
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_customer_job', 'customer_job_id', 'parent_category_id');
    }

    public function subCategories()
    {
        return $this->belongsToMany(Category::class, 'category_customer_job', 'customer_job_id', 'category_id');
    }



    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function getRegionNameAttribute(){
        if(!empty($this->region)){
            return $this->region->getTranslation('name', 'en');
        }else{
            return "";
        }
    }

    public function getCategoriesNameAttribute()
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

    public function getSubCategoriesNameAttribute()
    {
        $customer_categories_name = '';
        foreach($this->subCategories as $category) {
            $customer_categories_name .= $category->name . ', ';
        }

        if (strlen($customer_categories_name) > 2) {
            $customer_categories_name = substr($customer_categories_name, 0, -2);
        }

        return $customer_categories_name;

    }

    public function getSeoUrlAttribute(){
        return '/guide/'.  $this->id . '/' . Str::slug($this->permalink,'-',null);
    }


}
