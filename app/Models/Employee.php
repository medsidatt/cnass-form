<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'phone',
        'send_count',
        'last_sent_at',
        'verified_at',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->hasOne(Submission::class, 'phone', 'phone')->latestOfMany();
    }
}
