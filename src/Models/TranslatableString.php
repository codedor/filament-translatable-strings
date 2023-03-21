<?php

namespace Codedor\TranslatableStrings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TranslatableString extends Model
{
    use HasTranslations;

    protected $translatable = ['value'];

    protected $fillable = ['scope', 'name', 'key', 'is_html', 'value'];
}
