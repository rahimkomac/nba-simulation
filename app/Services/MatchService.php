<?php

namespace App\Services;


class MatchService {
    /**
     * @return bool
     */
    public function isAttack()
    {
        $attack = mt_rand(1, 100);
        $defend = mt_rand(1, 100);
        return (mt_rand(1, $attack) > $defend);
    }

    /**
     * @return int
     */
    public function points()
    {
        return mt_rand(1,3);
    }
}
