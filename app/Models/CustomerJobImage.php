<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerJobImage extends Model
{
    use HasFactory;

    protected $table = 'customer_job_image'; // Define the table name explicitly (if it's different from the default pluralized model name)

    protected $fillable = ['path']; // Define the fields that can be mass-assigned

    public function customerJob()
    {
        return $this->belongsTo(CustomerJob::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'customer_categories', 'customer_job_id', 'category_id');
    }
}
