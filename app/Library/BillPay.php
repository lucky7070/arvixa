<?php

namespace App\Library;

use Error;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class BillPay
{
    public static function getElectricityBill(string $consumer_no, string $provider,)
    {
        try {

            $response = Http::get('https://www.mplan.in/api/electricinfo.php', [
                'apikey'   => config('constant.mplan_key'),
                'offer'    => 'roffer',
                'tel'      => $consumer_no,
                'operator' => $provider,
            ]);

            $record = $response->json('records.0', []);
            return $record;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function getWaterBill(string $consumer_no, string $provider,)
    {
        try {

            $response = Http::get('https://www.mplan.in/api/water.php', [
                'apikey'   => config('constant.mplan_key'),
                'offer'    => 'roffer',
                'tel'      => $consumer_no,
                'operator' => $provider,
            ]);

            $record = $response->json('records.0', []);

            return $record;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function getLicPremium(string $consumer_no, string $provider, $email, $dob)
    {
        $response = Http::withOptions(['verify' => false])->get('https://connect.ekychub.in/v3/verification/bill_fetch', [
            'username'      => config('constant.ekychub_username'),
            'token'         => config('constant.ekychub_key'),
            'consumer_id'   => $consumer_no,
            'opcode'        => $provider,
            'value1'        => Carbon::parse($dob)->format('d-m-Y'),
            'value2'        => $email,
            'orderid'       => str()->uuid()->toString()
        ]);

      //  return ['userName' => 'Lucky test', 'billAmount' => 590];
        if ($response->json('status') === 'Success') {
            return $response->json('data.0');
        } else {
            throw new Error($response->json('message'));
        }
    }

    public static function getGasBill(string $consumer_no, string $provider,)
    {
        $response = Http::withOptions(['verify' => false])->get('https://connect.ekychub.in/v3/verification/bill_fetch', [
            'username'      => config('constant.ekychub_username'),
            'token'         => config('constant.ekychub_key'),
            'consumer_id'   => $consumer_no,
            'opcode'        => $provider,
            'orderid'       => str()->uuid()->toString()
        ]);

        if ($response->json('status') === 'Success') {
            return $response->json('data.0');
        } else {
            throw new Error($response->json('message'));
        }
    }
}
