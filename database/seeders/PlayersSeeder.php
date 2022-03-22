<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use JasonRoman\NbaApi\Client\Client;
use JasonRoman\NbaApi\Request\Data\Cms\Roster\TeamRosterRequest;

class PlayersSeeder extends Seeder
{
    /**
     * @param Team $team
     * @throws \Exception
     */
    public function run(Team $team)
    {
        $client = new Client();
        $request = TeamRosterRequest::fromArray([
            'teamSlug' => $team->slug,
        ]);

        $response = $client->request($request);
        $response = $response->getObjectFromJson();

        foreach ($response->sports_content->roster->players->player as $player) {
            if(empty($player->jersey_number)) {
                continue;
            }

            $model = new Player();
            $model->team_id = $team->id;
            $model->first_name = $player->first_name;
            $model->last_name = $player->last_name;
            $model->display_name = $player->display_name;
            $model->jersey_number = $player->jersey_number;
            $model->position_full = $player->position_full;
            $model->position_short = $player->position_short;
            $model->birth_date = Carbon::parse($player->birth_date)->format('Y-m-d');
            $model->save();
        }

        //$pool = new Pool();
        //$pool->
    }
}
