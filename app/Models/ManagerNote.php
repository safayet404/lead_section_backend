<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerNote extends Model
{
    protected $table = 'manager_notes';

    protected $fillable = ['lead_id', 'user_id', 'note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
