<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Match
 * @package App\Models
 * @property $id
 * @property $fixture_id
 * @property $home_points
 * @property $away_points
 * @property $home_attacks
 * @property $away_attacks
 */
class MatchModel extends Model
{
    public $timestamps = false;
    protected $table = 'matches';
    protected $fillable = ['fixture_id', 'home_points', 'away_points', 'home_attacks', 'away_attacks'];

    /**
     * @param $fixtureId
     * @return $this
     */
    public function start($fixtureId) {
        $this->fixture_id = $fixtureId;
        $this->home_points = 0;
        $this->away_points = 0;
        $this->home_attacks = 0;
        $this->away_attacks = 0;
        $this->save();
        return $this;
    }

    public function incrementHomePoint($point) {
        $this->increment('home_points', $point);
    }

    public function incrementAwayPoint($point) {
        $this->increment('away_points', $point);
    }

    public function incrementHomeAttack() {
        $this->increment('home_attacks');
    }

    public function incrementAwayAttack() {
        $this->increment('away_attacks');
    }


}
