<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpressApplication extends Model
{
    protected $fillable = [
        'full_name', 'email', 'country_of_residence', 'whatsapp_number',
        'country_to_apply', 'intake', 'course_type', 'university', 'course',
    ];

    public function files()
    {
        return $this->hasMany(ApplicationFile::class, 'application_id');
    }
}
