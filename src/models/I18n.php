<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Database\Eloquent\Model as Eloquent;

class I18n extends Eloquent
{
    protected $table = 'i18n';

    protected $fillable = [
        'locale',
        'name',
    ];

    protected $rules = [
        'locale' => 'required',
        'name'   => 'required',
    ];
}
