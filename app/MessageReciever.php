<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MessageReciever
 *
 * @property int message_id
 * @property int $reciever_id
 * @property int $viewed
 * @property string $viewedTms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $receivers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereTms($value)
 * @mixin \Eloquent
 */
class MessageReciever extends Model
{
	protected $table = 'message_reciever';

}
