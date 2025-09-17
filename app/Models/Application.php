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
        'created_by',
        'counsellor_phone',
        'counsellor_email',
    ];

    public function files()
    {
        return $this->hasMany(StudentApplicationFile::class, 'application_id');
    }

    public function assignedOfficer()
    {
return $this->hasOneThrough(User::class, AssignApplicationOfficer::class, 'application_id', 'id', 'id', 'user_id');

    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function applicationStatus()
    {
        return $this->belongsTo(ApplicationStatus::class);
    }

    public function channelPartner()
    {
        return $this->belongsTo(ChannelPartner::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
