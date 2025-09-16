<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentApplicationFile extends Model
{
    protected $fillable = [
        'application_id',
        'file_path',
        'file_type',
        'original_name',
        'file_size',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
