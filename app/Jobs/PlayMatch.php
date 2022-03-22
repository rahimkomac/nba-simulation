<?php

namespace App\Jobs;

use App\Models\Fixture;
use App\Services\MatchService;
use App\Services\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PlayMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    public $fixture;

    public function __construct(Fixture $fixture)
    {
        $this->fixture = $fixture;
    }

    public function handle()
    {
        try {
            $playerService = new Player();
            $matchService = new MatchService();
            $homeTeamPlayers = $playerService->getPlayers($this->fixture->home_team_id);
            $homeTeamCurrentPlayers = $homeTeamPlayers->take(5)->toArray();

            $awayTeamPlayers = $playerService->getPlayers($this->fixture->away_team_id);
            $awayTeamCurrentPlayers = $awayTeamPlayers->take(5)->toArray();

            $this->fixture->changeState(Fixture::MATCH_PLAYING);
            $match = new \App\Models\MatchModel();
            $match->start($this->fixture->id);
            $playerService->savePlayers($match->id, $homeTeamCurrentPlayers);
            $playerService->savePlayers($match->id, $awayTeamCurrentPlayers);

            $time = 0;
            $attackingTeam = $this->fixture->home_team_id;
            $homeTeamChangePlayerTime = rand(20, 220);
            $awayTeamChangePlayerTime = rand(15, 230);
            $hasHomeTeamChangePlayers = false;
            $hasAwayTeamChangePlayers = false;
            $playerStats = [];
            while ($time <= 240) {
                sleep(1);
                if($homeTeamChangePlayerTime == $time) {
                    $hasHomeTeamChangePlayers = true;
                    $homeTeamCurrentPlayers = $playerService->saveChangePlayer($playerService, $match->id, $homeTeamPlayers, $homeTeamCurrentPlayers);
                }

                if($awayTeamChangePlayerTime == $time) {
                    $hasAwayTeamChangePlayers = true;
                    $awayTeamCurrentPlayers = $playerService->saveChangePlayer($playerService, $match->id, $awayTeamPlayers, $awayTeamCurrentPlayers);
                }

                if($attackingTeam == $this->fixture->home_team_id) {
                    $possiblePlayers = $playerService->possiblePlayers($homeTeamCurrentPlayers);
                } else {
                    $possiblePlayers = $playerService->possiblePlayers($awayTeamCurrentPlayers);
                }

                $point = 0;
                if ($matchService->isAttack()) {
                    $point = $matchService->points();
                    $playerStats = $playerService->setPointStat($playerStats, $possiblePlayers['possibleBasketPlayer']['id'], $point);
                    $playerStats = $playerService->setAssistStat($playerStats, $possiblePlayers['possibleAssistPlayer']['id']);

                    // save points
                    $playerService->updatePlayerStat(
                        $match->id,
                        $possiblePlayers['possibleBasketPlayer']['id'],
                        ['points' => $playerStats[$possiblePlayers['possibleBasketPlayer']['id']]['points']]
                    );

                    // save assist
                    $playerService->updatePlayerStat(
                        $match->id,
                        $possiblePlayers['possibleAssistPlayer']['id'],
                        ['assist' => $playerStats[$possiblePlayers['possibleAssistPlayer']['id']]['assist']]
                    );
                } else {
                    if ($attackingTeam == $this->fixture->home_team_id) {
                        $attackingTeam = $this->fixture->away_team_id;
                    } else {
                        $attackingTeam = $this->fixture->home_team_id;
                    }
                }

                if($attackingTeam == $this->fixture->home_team_id) {
                    $match->incrementHomeAttack();
                    $match->incrementHomePoint($point);
                } else {
                    $match->incrementAwayAttack();
                    $match->incrementAwayPoint($point);
                }

                $homeTeamPlayerWithStats = $playerService->playersMergeStats($homeTeamCurrentPlayers, $playerStats, $hasHomeTeamChangePlayers);
                $awayTeamPlayerWithStats = $playerService->playersMergeStats($awayTeamCurrentPlayers, $playerStats, $hasAwayTeamChangePlayers);

                broadcast(new \App\Events\MatchEvent([
                    'matchId' => $match->id,
                    'fixtureId' => $this->fixture->id,
                    'state'     => $time == 240 ? 'finished' : 'playing',
                    'homeTeam' => $this->fixture->homeTeam->short_name,
                    'homePoints' => $match->home_points,
                    'dash' => $time == 240 ? 'Finished' : 'Playing',
                    'awayPoints' => $match->away_points,
                    'awayTeam' => $this->fixture->awayTeam->short_name,
                    'players' => [
                        'homeTeam' => $homeTeamPlayerWithStats,
                        'awayTeam' => $awayTeamPlayerWithStats,
                    ],
                ]));

                $time++;
            }
            $this->fixture->changeState(Fixture::MATCH_DONE);
        } catch (\Throwable $e) {
            Log::warning($e->getFile());
            Log::warning($e->getLine());
            Log::warning($e->getMessage());
            Log::warning($e->getTraceAsString());
        }
    }
}
