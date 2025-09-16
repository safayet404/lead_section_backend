<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name', 'university_id', 'country_id', 'intake_id',
        'course_type', 'duration', 'tuition_fee', 'course_type_id',
        'academic_requirement', 'english_requirement',
    ];

    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }
}
