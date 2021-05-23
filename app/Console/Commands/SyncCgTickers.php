<?php

namespace App\Console\Commands;

use App\Models\CgExchange;
use App\Models\CgTicker;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Console\Command;

class SyncCgTickers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cg:tickers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise Coin Gecko API data (tickers)';

    private $client;

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
        $exchanges = CgExchange::all();
        $count = count($exchanges);
        $exchanges->each(function ($exchange, $index) use ($count) {
            $this->info('Refreshing tickers for exchange: '.$exchange->id.'('.($index + 1).' of '.$count.')');
            $this->clearTickers($exchange->id);
            $this->storeExchangeTickers($exchange->id);
        });
    }

    private function clearTickers(string $exchangeId)
    {
        CgTicker::where('exchange_id', $exchangeId)->delete();
    }

    private function storeExchangeTickers(string $exchangeId, int $page = 1)
    {
        $this->setClient();
        $data = $this->client->exchanges()->getTickers($exchangeId, [
            'page'     => $page,
        ]);
        $this->info('Retrieved '.count($data['tickers']).' tickers (API page '.$page.')');
        if (0 === count($data['tickers'])) {
            $this->info('No tickers found: Moving to next exchange');

            return true;
        }
        collect($data['tickers'])->each(function ($ticker) use ($exchangeId) {
            $ticker['exchange_id'] = $exchangeId;
            CgTicker::create($ticker);
        });
        sleep(5);
        ++$page;

        return $this->storeExchangeTickers($exchangeId, $page);
    }
}
