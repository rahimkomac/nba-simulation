<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlayerStat
 * @package App\Models
 * @property $id
 * @property $match_id
 * @property $player_id
 * @property $status
 * @property $assist
 * @property $points
 */

class PlayerStat extends Model
{
    const CURRENT_PLAYER = 0;
    const IN_PLAYER = 1;
    const OUT_PLAYER = -1;

    public $timestamps = false;

    protected $table = 'player_stats';
    protected $fillable = [
        'match_id', 'player_id', 'status', 'assist', 'points', 'pass'
    ];

    public static function savePlayer($matchId, $playerId, $params = []) {
        $updateOptions = [];
        if(isset($params['status'])) {
            $updateOptions['status'] = $params['status'];
        }
        if(isset($params['pass'])) {
            $updateOptions['pass'] = $params['pass'];
        }
        if(isset($params['assist'])) {
            $updateOptions['assist'] = $params['assist'];
        }
        if(isset($params['points'])) {
            $updateOptions['points'] = $params['points'];
        }
        self::updateOrCreate(
            ['match_id' => $matchId, 'player_id' => $playerId],
            $updateOptions
        );
    }

}
