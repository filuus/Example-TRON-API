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

    /**
     * @OA\Post(
     *      path="/wallet",
     *      operationId="createWallet",
     *      summary="Create new wallet",
     *      description="Create new wallet",
     *      tags={"Wallets"},
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       )
     *
     *     )
     */

    public function createWallet()
    {
        $tron = SELF::getTron();
        $account = $tron->createAccount();
        $wallet = new Wallet;
        $wallet->address = $account['address'];
        $wallet->secret = $account['privateKey'];
        $wallet->save();
        return response()->json(['address' => $account['address']], 201);
    }

    /**
     * @OA\Get(
     *      path="/balance/{address}",
     *      operationId="getWalletBalance",
     *      summary="Get balance of wallet",
     *      description="Get the balance of wallet",
     *      tags={"Wallets"},
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         description="The address of wallet",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       )
     *
     *     )
     */

    public function getBalanceOfWallet(string $address)
    {
        $tron = SELF::getTron();
        $tron->setAddress($address);
        $balance = $tron->getBalance(null, true);
        return response()->json(['balance' => $balance], 200);
    }
}
