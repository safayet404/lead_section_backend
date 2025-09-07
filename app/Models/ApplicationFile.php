<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationFile extends Model
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
        return $this->belongsTo(ExpressApplication::class,'application_id');
    }
}
