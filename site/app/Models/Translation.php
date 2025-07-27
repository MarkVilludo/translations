<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_key_id',
        'content',
        'locale_id',
    ];

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class);
    }
}