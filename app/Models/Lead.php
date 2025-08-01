<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'lead_date',
        'email',
        'name',
        'phone',
        'interested_course',
        'interested_country',
        'current_qualification',
        'ielts_or_english_test',
        'soruce',
        'status_id',
        'notes',
        'assigned_branch',
        'assigned_user',
        'created_by'
    ];
}
