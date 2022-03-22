<?php

namespace Database\Seeders;

use App\Models\Fixture;
use App\Services\Fixture as MatchFixture;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixtureSeeder extends Seeder
{
    /**
     * @param array $teamIds
     */
    public function run(array $teamIds = [])
    {
        $fixture = new MatchFixture($teamIds);
        $schedules = $fixture->getSchedule();
        $scheduleDate = Carbon::now();
        foreach ($schedules as $schedule) {
            foreach ($schedule as $match) {
                $fixture = new Fixture();
                $fixture->home_team_id =  $match[0];
                $fixture->away_team_id =  $match[1];
                $fixture->schedule =  $scheduleDate->format('Y-m-d');
                $fixture->status = Fixture::MATCH_PENDING;
                $fixture->save();
            }
            $scheduleDate->addWeek();
        }
    }
}
