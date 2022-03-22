<?php
namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Services\MatchService;
use App\Services\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getMatchData();
        $matchData = $data['matchData'];
        $schedule = $data['schedule'];
        $isPlaying = $data['isPlaying'];

        return view('welcome', compact('matchData', 'schedule', 'isPlaying'));
    }

    public function nextFixture(Request $request)
    {
        $data = $this->getMatchData();
        $matchData = $data['matchData'];
        $schedule = $data['schedule'];
        Artisan::call('simulate:match');
        return response()->json([
            'status'    => 'success',
            'matchData' => $matchData,
            'schedule' => $schedule,
            'isPlaying' => true,
        ]);
    }

    private function getMatchData() {
        $fixtures = Fixture::getCurrentFixtures();
        $matchData = [];
        foreach ($fixtures as $fixture) {
            $matchData[] = [
                'matchId' => 0,
                'fixtureId' => $fixture->id,
                'homeTeam' => $fixture->homeTeam->short_name,
                'homePoints' => 0,
                'dash' => 'waiting',
                'awayPoints' => 0,
                'awayTeam' => $fixture->awayTeam->short_name,
                'players' => [],
            ];
        }
        $schedule = $fixtures[0]->schedule->format('F d');
        $isPlaying = $fixtures[0]->status == 1;

        return [
            'matchData' => $matchData,
            'schedule' => $schedule,
            'isPlaying' => $isPlaying,
        ];
    }
}
