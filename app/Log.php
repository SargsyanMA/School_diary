<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Builder;

/**
 * App\Log
 *
 * @property int $id
 * @property string|null $class
 * @property string|null $method
 * @property string|null $userId
 * @property string|null $query
 * @property string|null $tms
 *
 * @method static Builder|Log query()
 * @method static Builder|Log whereCode($value)
 * @method static Builder|Log whereCreatedAt($value)
 * @method static Builder|Log whereId($value)
 * @method static Builder|Log whereName($value)
 * @method static Builder|Log whereUpdatedAt($value)
 */

class Log extends Model
{
    protected $table = 'log';

    /**
     * Логирует действия. Имя дурное потому что просто save уже занят.
     * @param string $class
     * @param string $method
     * @param string $operation
     * @param int $userId
     * @param array $data
     */
    public static function saveExternal($class, $method, $operation, $userId, $data): void
    {
        DB::table('log')->insert([
            'class' => $class,
            'method' => $method,
            'operation' => $operation,
            'userId' => $userId,
            'query' => json_encode($data),
            'tms' => Carbon::now()
        ]);
    }
}
