<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Team
 * @package App\Models
 * @property $id
 * @property $team_id
 * @property $first_name
 * @property $last_name
 * @property $display_name
 * @property $jersey_number
 * @property $position_full
 * @property $position_short
 * @property $birth_date
 */
class Player extends Model
{
    public $timestamps = false;

    protected $table = 'players';
    protected $fillable = [
        'team_id','first_name', 'last_name', 'display_name',
        'jersey_number', 'position_full', 'position_short','birth_date'
    ];
    protected $dates = ['birth_date'];
}
