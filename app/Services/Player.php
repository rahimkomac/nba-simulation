<?php

namespace App\Services;

use App\Models\PlayerStat;
use Illuminate\Support\Collection;

class Player {
    /**
     * @param $teamId
     * @return \Illuminate\Support\Collection
     */
    public function getPlayers($teamId)
    {
        return collect(\App\Models\Player::where('team_id', $teamId)->get())
            ->map(function ($item, $key) {
                $item['points'] = 0;
                $item['assist'] = 0;
                $item['status'] = 0;
                return $item;
            })
            ->shuffle();
    }

    /**
     * @param $currentPlayers
     * @return array
     */
    public function possiblePlayers($currentPlayers) {
        $currentPlayers = collect($currentPlayers)->where('isOut','!=', true)->toArray();
        $currentPlayer = $currentPlayers[rand(0,4)];
        $totalPassCount = rand(1,8);
        $pass = 0;
        while (true) {
            $possibleAssistPlayer = $currentPlayer;
            $nextPlayer = $currentPlayers[rand(0,4)];
            if($nextPlayer != $currentPlayer) {
                $pass++;
            }

            if($pass >= $totalPassCount) {
                $possibleBasketPlayer = $nextPlayer;
                return [
                    'possibleBasketPlayer' => $possibleBasketPlayer,
                    'possibleAssistPlayer' => $possibleAssistPlayer
                ];
            }
            $currentPlayer = $nextPlayer;
        }
    }

    /**
     * @param Collection $players
     * @param Collection $currentPlayers
     * @return array
     */
    public function changePlayer(Collection $players, Collection $currentPlayers) {
        $currentPlayerIds = $currentPlayers->pluck('id')->toArray();
        // sahada olan 5li değiştirilebilir oyuncular arasından çıkarılıyor.
        $changeablePlayers = $players->filter(function ($value, $key) use($currentPlayerIds) {
            return ! in_array($value['id'], $currentPlayerIds);
        });

        $changeablePlayers->shuffle();
        $currentPlayers->shuffle();
        $enteringPlayer = $changeablePlayers->pop();
        $outPlayer = $currentPlayers->pop();
        $enteringPlayer['isIn'] = true;
        $outPlayer['isOut'] = true;
        $currentPlayers->push($enteringPlayer);
        $currentPlayers->push($outPlayer);
        return [
            'in' => $enteringPlayer,
            'out'=> $outPlayer,
            'team' => $currentPlayers
        ];
    }

    public function savePlayers($matchId, $players) {
        foreach ($players as $player) {
            $this->updatePlayerStat(
                $matchId,
                $player['id'],
                ['status' => PlayerStat::CURRENT_PLAYER, 'pass' => 0, 'assist' => 0, 'points' => 0]
            );
        }
    }

    public function updatePlayerStat($matchId, $playerId, $params) {
        PlayerStat::savePlayer($matchId, $playerId, $params);
    }

    public function saveChangePlayer(Player $playerService, $matchId,  $teamPlayers, $teamCurrentPlayers) {
        $teamCurrentPlayers = $playerService->changePlayer(
            $teamPlayers->shuffle(),
            collect($teamCurrentPlayers)
        );

        // save in player change
        $playerService->updatePlayerStat(
            $matchId,
            $teamCurrentPlayers['in']['id'],
            ['status' => PlayerStat::IN_PLAYER]
        );

        // save out player change
        $playerService->updatePlayerStat(
            $matchId,
            $teamCurrentPlayers['out']['id'],
            ['status' => PlayerStat::OUT_PLAYER]
        );

        return $teamCurrentPlayers['team'];
    }

    public function playersMergeStats($players, $playerStats, $changePlayers = false) {
        $playerWithStats = [];
        foreach ($players as $currentPlayer) {
            $status = 0;
            if($changePlayers) {
                if(isset($currentPlayer['isIn'])) {
                    $status = 1;
                }
                if(isset($currentPlayer['isOut'])) {
                    $status = -1;
                }
            }

            $playerWithStats[] = [
                'id' => $currentPlayer['id'].'-'.$status,
                'name' => $currentPlayer['display_name'],
                'status' => $status,
                'points' => $playerStats[$currentPlayer['id']]['points'] ?? 0,
                'assist' => $playerStats[$currentPlayer['id']]['assist'] ?? 0,
            ];
        }

        return $playerWithStats;
    }

    public function setPointStat($stats, $playerId, $point) {
        if(!isset($stats[$playerId])) {
            $stats[$playerId] = [
                'points' => 0,
                'assist' => 0
            ];
        }

        $stats[$playerId]['points'] =
            $stats[$playerId]['points'] + $point;

        return $stats;
    }

    public function setAssistStat($stats, $playerId) {
        if(!isset($stats[$playerId])) {
            $stats[$playerId] = [
                'points' => 0,
                'assist' => 0
            ];
        }

        $stats[$playerId]['assist']++;

        return $stats;
    }
}
