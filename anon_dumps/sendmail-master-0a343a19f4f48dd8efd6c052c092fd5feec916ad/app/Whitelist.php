<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Whitelist
 *
 * @property int $id
 * @property string $base64
 * @method static \Illuminate\Database\Query\Builder|\App\Whitelist whereBase64($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Whitelist whereId($value)
 * @mixin \Eloquent
 */
class Whitelist extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['base64'];
    public $timestamps = false;
}
