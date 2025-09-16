<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = ['name'];

    public function universities()
    {
        return $this->hasMany(University::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
