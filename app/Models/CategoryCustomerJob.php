<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCustomerJob extends Model
{
    use HasFactory;

    protected $table = 'category_customer_job';

    public function customerJob()
    {
        return $this->belongsTo(CustomerJob::class);
    }
}
