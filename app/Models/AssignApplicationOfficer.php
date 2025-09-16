<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignApplicationOfficer extends Model
{
    protected $fillable = [
        'application_id',
        'user_id'
    ];
}
