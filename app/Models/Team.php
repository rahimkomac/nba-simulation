<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Team
 * @package App\Models
 * @property $id
 * @property $name
 * @property $nickname
 * @property $short_name
 * @property $abbrev
 * @property $city
 * @property $slug
 */
class Team extends Model
{
    public $timestamps = false;

    protected $table = 'teams';
    protected $fillable = ['name', 'nickname', 'short_name', 'abbrev', 'city', 'slug'];
}
