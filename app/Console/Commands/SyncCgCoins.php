<?php

namespace App\Console\Commands;

use App\Models\CgCoin;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Console\Command;

class SyncCgCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cg:coins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise Coin Gecko API data (coins)';

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
     * @return int
     */
    public function handle()
    {
        $client = new CoinGeckoClient();
        $data = $client->coins()->getList();
        collect($data)->each(function ($coin) {
            CgCoin::updateOrCreate(
                [
                    'id' => $coin['id'],
                ],
                $coin
            );
        });
    }
}
