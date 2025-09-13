<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
     protected $fillable = [
        'student_id',
        'country_id',
        'intake_id',
        'course_type_id',
        'university_id',
        'course_id',
        'passport_country',
        'channel_partner_id',
        'application_status_id',
        'branch_id',
        'created_by'
    ];
}
