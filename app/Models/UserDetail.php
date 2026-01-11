<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'creating_account_for_me',
        'name',
        'dob',
        'height',
        'weight',
        'gender',
        'marital_status',
        'current_location',
        'mother_tongue',
        'known_languages'
    ];

    protected $casts = [
        'known_languages' => 'array',
        'dob' => 'date',
        'creating_account_for_me' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
