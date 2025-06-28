<?php

namespace App\Library;

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

    public static function getLicPremium(string $consumer_no, string $provider,)
    {
        try {

            $response = Http::get('https://www.mplan.in/api/lic.php', [
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

    public static function getGasBill(string $consumer_no, string $provider,)
    {
        try {

            $response = Http::get('https://www.mplan.in/api/gas.php', [
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
}
