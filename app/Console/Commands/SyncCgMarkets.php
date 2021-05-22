<?php

namespace App\Console\Commands;

use App\Models\CgCoin;
use App\Models\CgMarket;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Console\Command;

class SyncCgMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cg:markets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise Coin Gecko API data (markets)';

    private $client;

    /**
     * Results per page for the query.
     */
    private $per_page = 250;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function setClient()
    {
        if (! $this->client) {
            $this->client = new CoinGeckoClient();
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pages = ceil(CgCoin::count() / $this->per_page);
        for ($i = 1; $i <= $pages; ++$i) {
            $this->sync($i);
            sleep(1);
        }
    }

    private function sync($page)
    {
        $this->setClient();
        $data = $result = $this->client->coins()->getMarkets('usd', [
            'page'     => $page,
            'per_page' => $this->per_page,
        ]);
        collect($data)->each(function ($market) {
            CgMarket::updateOrCreate(
                [
                    'id' => $market['id'],
                ],
                $market
            );
        });
    }
}
