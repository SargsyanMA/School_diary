<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScoreType
 *
 * @property int $id
 * @property string $name
 * @property float $weight
 * @property int|null $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScoreType whereWeight($value)
 * @mixin \Eloquent
 */
class ScoreType extends Model
{
    protected $table = 'score_types';
}
