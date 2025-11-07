<?php

namespace App\Console\Commands;

use App\Models\PlinkoGame;
use Illuminate\Console\Command;

class FlushPlinkoGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plinko:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the plinko_games table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PlinkoGame::truncate();
        $this->info('The plinko_games table has been flushed.');
    }
}