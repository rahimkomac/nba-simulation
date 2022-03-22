<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use Illuminate\Console\Command;

class Simulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:match';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate matches';

    protected $simulate;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fixtures = Fixture::getCurrentFixtures();
        foreach ($fixtures as $fixture) {
            /** @var $fixture Fixture */
            dispatch(new \App\Jobs\PlayMatch($fixture))
                ->onQueue(config('queue.connections.redis.queue'));
        }


    }
}
