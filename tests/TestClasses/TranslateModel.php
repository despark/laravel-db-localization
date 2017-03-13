<?php


namespace Despark\Tests\LaravelDbLocalization\TestClasses;


use Despark\LaravelDbLocalization\Contracts\Translatable;
use Despark\LaravelDbLocalization\Traits\HasTranslation;
use Illuminate\Database\Eloquent\Model;

class TranslateModel extends Model implements Translatable
{
    use HasTranslation;

    protected $translatable = ['field_1', 'field_2'];

    protected $fillable = ['not_translatable'];

    protected $table = 'translate_test';

}