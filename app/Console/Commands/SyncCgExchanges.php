<?php

namespace App\Console\Commands;

use App\Models\CgExchange;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Console\Command;

class SyncCgExchanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cg:exchanges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise Coin Gecko API data (exchanges)';

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
        $data = $client->exchanges()->getExchanges();
        collect($data)->each(function ($exchange) {
            CgExchange::updateOrCreate(
                [
                    'id' => $exchange['id'],
                ],
                $exchange
            );
        });
    }
}
