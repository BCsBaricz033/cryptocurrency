<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Wallet;
use App\Models\Coin;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Support\Facades\Auth;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class RefreshCoinsList implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    { 
        $wallets = Wallet::where('user_id', $this->user->id)->orderBy('name')->get();
        $coins = Coin::all();
        $client = new CoinGeckoClient();
        $bitcoin=null;
        $marketData = $client->coins()->getMarkets('usd');
        foreach ($marketData as $marketCoin) {
            if($marketCoin['id']=='bitcoin'){
                $bitcoin=$marketCoin['current_price'];
            }
            $coin = $coins->where('id', $marketCoin['id'])->first();
            if ($coin) {
                $coin->price = $marketCoin['current_price'];
                $coin->price_change_percentage_24h = $marketCoin['price_change_percentage_24h'];
                $coin->price_change_24h = $marketCoin['price_change_24h'];
                $coin->image=$marketCoin['image'];
                $coin->save();
            } else if (!$coin) {
                DB::table('coins')->insert([
                    'id' => $marketCoin['id'],
                    'name' => $marketCoin['name'],
                    'price' => $marketCoin['current_price'],
                    'price_change_percentage_24h' => $marketCoin['price_change_percentage_24h'],
                    'price_change_24h' => $marketCoin['price_change_24h'],

                ]);
            }
            $wallet = $wallets->where('api_id', $marketCoin['id'])->first();

            if ($wallet) {
                $wallet->price = $marketCoin['current_price'];
                $wallet->price_change_percentage_24h = $marketCoin['price_change_percentage_24h'];
                $wallet->price_change_24h = $marketCoin['price_change_24h'];
                $wallet->save();
            }

        }
        
        if($bitcoin>50000){
            Mail::to($this->user->email)->send(new NotificationEmail($this->user->name, $bitcoin));
        }

            
    }
}
