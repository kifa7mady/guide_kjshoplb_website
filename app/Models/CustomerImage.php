<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerImage extends Model
{
    use HasFactory;

    protected $table = 'customer_image'; // Define the table name explicitly (if it's different from the default pluralized model name)

    protected $fillable = ['path']; // Define the fields that can be mass-assigned

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}

