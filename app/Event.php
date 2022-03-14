<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Event
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $date2
 * @property string|null $title
 * @property string|null $text
 * @property string|null $tms
 * @property int|null $author
 * @property int $active
 * @property int|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereTms($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    protected $table = 'event';
}
