<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Team
 * @package App\Models
 * @property $id
 * @property $schedule
 * @property $home_team_id
 * @property $away_team_id
 * @property $status
 */
class Fixture extends Model
{
    const MATCH_PENDING = 0;
    const MATCH_PLAYING = 1;
    const MATCH_DONE = 2;

    public $timestamps = false;

    protected $table = 'fixtures';
    protected $fillable = ['schedule', 'home_team_id', 'away_team_id', 'status'];
    protected $dates = ['schedule'];

    public function homeTeam()
    {
        return $this->hasOne('App\Models\Team', 'id', 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->hasOne('App\Models\Team', 'id', 'away_team_id');
    }

    public function changeState(int $state)
    {
        $this->status = $state;
        $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Fixture[]
     */
    public static function getCurrentFixtures() {
        $matchState = self::MATCH_PLAYING;
        $date = self::where('status', self::MATCH_PLAYING)->orderBy('schedule')->pluck('schedule')->first();
        if(empty($date)) {
            $matchState = self::MATCH_PENDING;
            $date = self::where('status', self::MATCH_PENDING)->orderBy('schedule')->pluck('schedule')->first();
        }
        return self::with(['homeTeam', 'awayTeam'])->where('status', $matchState)
            ->where('schedule', $date)
            //->where('id', 1)
            ->get();
    }
}
