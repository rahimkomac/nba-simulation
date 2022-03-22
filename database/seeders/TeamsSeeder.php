<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use JasonRoman\NbaApi\Client\Client;
use JasonRoman\NbaApi\Request\Data\Cms\Teams\SportsMetaTeamsRequest;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $request = SportsMetaTeamsRequest::fromArray();
        $response = $client->request($request);
        $response = $response->getObjectFromJson();

        $teamIds = [];
        foreach ($response->sports_content->teams->team as $team) {
            if(!$team->is_nba_team || $team->team_id == 1610616839) {
                continue;
            }

            $model = new Team();
            $model->name = $team->team_name;
            $model->nickname = $team->team_nickname;
            $model->short_name = $team->team_short_name;
            $model->abbrev = $team->team_abbrev;
            $model->city = $team->city;
            $model->slug = $team->team_code;
            $model->save();
            $teamIds[] = $model->id;
            $this->call(PlayersSeeder::class, false, ['team' => $model]);
        }

        $this->call(FixtureSeeder::class, false, ['teamIds' => $teamIds]);
    }
}
