<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wallet;

use IEXBase\TronAPI\Tron;
use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Exception\TronException;



class TronController extends Controller
{
    public function getTron()
    {
        $fullNode = new HttpProvider('https://api.trongrid.io');
        $solidityNode = new HttpProvider('https://api.trongrid.io');
        $eventServer = new HttpProvider('https://api.trongrid.io');

        try {
            $tron = new Tron($fullNode, $solidityNode, $eventServer);
            return $tron;
        } catch (TronException $e) {
            exit($e->getMessage());
        }
    }

    public function createWallet()
    {
        $tron = SELF::getTron();
        $account = $tron->createAccount();
        $wallet = new Wallet;
        $wallet->address = $account['address'];
        $wallet->secret = $account['privateKey'];
        $wallet->save();
        return response()->json(['address' => $account['address']]);
    }

    public function getBallanceOfWallet()
    {
        
    }
}
