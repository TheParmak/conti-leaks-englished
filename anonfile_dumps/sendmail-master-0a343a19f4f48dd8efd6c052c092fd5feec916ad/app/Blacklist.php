<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Blacklist
 *
 * @property int $id
 * @property string $base64
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property bool $valid
 * @method static \Illuminate\Database\Query\Builder|\App\Blacklist whereBase64($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Blacklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Blacklist whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Blacklist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Blacklist whereValid($value)
 * @mixin \Eloquent
 */
class Blacklist extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['base64'];
    public $timestamps = true;

    /**
     * @return string
     */
    public static function table()
    {
        return with(new static)->getTable();
    }

    /**
     * Insert each item as a row. Does not generate events.
     *
     * @param  array  $items
     *
     * @return bool
     */
    public static function insertAll(array $items)
    {
        $now = \Carbon\Carbon::now();
        $items = collect($items)->map(function (array $data) use ($now) {
            return array_merge([
                'created_at' => $now
            ], $data);
        })->all();
        $tmp = false;
        try {
            $tmp = \DB::table(static::table())->insert($items);
        } catch (Exception $e) {
            $tmp = false;
        }
        return $tmp;
    }
}
