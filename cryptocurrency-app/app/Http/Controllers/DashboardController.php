<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use App\Jobs\RefreshCoinsList;
class DashboardController extends Controller
{
    public function dashboard()
    {
        $wallets = Wallet::where('user_id', Auth::user()->getAuthIdentifier())->orderBy('name')->get();
        $user = Auth::user();
       
        RefreshCoinsList::dispatch($user);
         $wallets = Wallet::where('user_id', Auth::user()->getAuthIdentifier())->orderBy('name')->get();
        return Inertia::render('Dashboard', [
            'wallets'=> $wallets,
            'user'=>$user,
            
        ]);
    }
}
