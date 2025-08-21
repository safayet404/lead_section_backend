<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    protected $table = 'lead_statues';

    protected $fillable = ['name', 'color_code', 'health_type'];
}
