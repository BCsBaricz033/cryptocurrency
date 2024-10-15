<?php

namespace App\Http\Controllers;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Coin;
use App\Jobs\RefreshCoinsList;


class CriptoController extends Controller
{
    public function getCriptocurrenciesList()
    {
        $coins = Coin::all();
        $wallets = Wallet::where('user_id', [Auth::user()->getAuthIdentifier()])->orderBy('name')->get();
        $walletsName = $wallets->pluck('name');
        $filteredData = $coins->filter(function ($i) use ($walletsName) {
            return !$walletsName->contains($i['name']);
        });

        return ($filteredData->values()->all());
    }
    public function save(Request $request)
    {
        DB::insert('insert into wallets (api_id, user_id, name, amount, price, total, image, price_change_24h, price_change_percentage_24h) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $request->selectedcurrency['id'],
            $request->user['id'],
            $request->selectedcurrency['name'],
            $request->amount,
            $request->price,
            $request->total,
            $request->selectedcurrency['image'],
            $request->selectedcurrency['price_change_24h'],
            $request->selectedcurrency['price_change_percentage_24h'],
        ]);
        
        $user = User::find($request->user['id']);
        $user->balance = $user->balance - $request->total;
        $user->save();
    }
    public function update(Request $request)
    {
        $wallet = Wallet::find($request->selectedcurrency['id']);
        $user = User::find($request->user['id']);
        $user->balance = $user->balance + $wallet->total;
        $user->balance = $user->balance - $request->total;
        $user->save();
        $wallet->amount = $request->amount;
        $wallet->price = $request->price;
        $wallet->total = $request->total;
        $wallet->save();
    }
    public function delete(Request $request)
    {
        $wallet = Wallet::find($request->selectedcurrency['id']);
        $user = User::find($request->user['id']);
        $user->balance = $user->balance + $wallet->total;
        $user->save();


        $wallet->delete();
    }
    public function refresh()
    {
        RefreshCoinsList::dispatch(Auth::user());
        

    }
}
