<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'passport_number',
        'passport_country',
        'branch_id',
        'address',
        'city',
        'gender',
        'visa_refusal'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
