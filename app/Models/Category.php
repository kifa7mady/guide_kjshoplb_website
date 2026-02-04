<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory,HasTranslations;
    protected $fillable = ['name','logo','parent_id'];
    public $translatable = ['name'];

//    public function customers()
//    {
//        return $this->belongsToMany(Customer::class, 'customer_categories', 'category_id', 'customer_id');
//    }

    public function customerJobsByCategory()
    {
        return $this->hasManyThrough(
            CustomerJob::class,
            CategoryCustomerJob::class,
            'category_id', // Foreign key on category_customer_job table
            'id', // Foreign key on customer_jobs table
            'id', // Local key on categories table
            'customer_job_id' // Local key on category_customer_job table
        );
    }

    public function customerJobsByParentCategory()
    {
        return $this->hasManyThrough(
            CustomerJob::class,
            CategoryCustomerJob::class,
            'parent_category_id', // Foreign key on category_customer_job table
            'id', // Foreign key on customer_jobs table
            'id', // Local key on categories table
            'customer_job_id' // Local key on category_customer_job table
        );
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relationship to get the parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }




    public function getSeoUrlAttribute(){
        if(!empty(request()->cookie('region_id'))){
            $region = Region::find(request()->cookie('region_id'));
        }else{
            $region = Region::find(request()->segment(3));
        }
        if($this->parent_id > 0){
            return '/guide/' . (!empty($region) ? Str::slug($this->getTranslation('name', 'en')) : 'All' ) .'/'.  $this->parent_id . '/' . Str::slug($this->name) . '/'. $this->id;;
        }else{
            return '/guide/' . (!empty($region) ? Str::slug($this->getTranslation('name', 'en')) : 'All' ) .'/'.  $this->id . '/' . Str::slug($this->name);;
        }
    }
}
