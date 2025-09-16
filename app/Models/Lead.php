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
        'created_by',
        'event_id',
        'lead_type',
        'assign_id',
        'lead_country',
        'lead_branch',
        'assigned_at',
    ];

    public function status()
    {
        return $this->belongsTo(LeadStatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_user');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'assigned_branch');
    }

    public function type()
    {
        return $this->belongsTo(LeadType::class, 'lead_type');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function assign_type()
    {
        return $this->belongsTo(AssignType::class, 'assign_id');
    }

    public function lead_country()
    {
        return $this->belongsTo(Country::class, 'lead_country');
    }

    public function lead_branch()
    {
        return $this->belongsTo(Branch::class, 'lead_branch');
    }

    public function note()
    {
        return $this->hasMany(ManagerNote::class);
    }

    protected $casts = [
        'assigned_at' => 'datetime',
    ];
}
