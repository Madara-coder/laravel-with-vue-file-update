<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class File extends Model
{
    protected $fillable = [
        'user_id', 'original_name', 'stored_name',
        'path', 'mime_type', 'size', 'share_token'
    ];

    protected $casts = ['size' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generateShareToken(): string
    {
        $this->share_token = Str::random(32);
        $this->save();
        return $this->share_token;
    }
}