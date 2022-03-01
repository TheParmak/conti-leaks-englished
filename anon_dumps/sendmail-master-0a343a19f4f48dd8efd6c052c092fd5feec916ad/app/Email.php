<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Email
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @method static \Illuminate\Database\Query\Builder|\App\Email whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Email whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Email whereTitle($value)
 * @mixin \Eloquent
 */
class Email extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'body', 'from', 'simple_body', 'type'];
    protected $guarded = ['id'];
}
