<?php

namespace App\Library;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PanCard
{
    protected static function getHeader()
    {
        return [
            'x-api-key'         => config('constant.nsdl.x_api_key'),
            'x-api-username'    => config('constant.nsdl.x_api_username'),
        ];
    }

    public static function newPan($user)
    {
        $data = [
            'name'          => $user['name'],
            'middle_name'   => $user['middle_name'],
            'last_name'     => $user['last_name'],
            'email'         => $user['email'],
            'phone'         => $user['phone'],
            'gender'        => $user['gender'],
            'dob'           => Carbon::parse($user['dob'])->format('Y-m-d'),
            'return_url'    => url(config('constant.nsdl.return_url')),
            'IsPhyPan'      => !empty($user['card_type']) ? $user['card_type'] : "Y",
            'kyc_type'      => !empty($user['kyc_type']) ? $user['kyc_type'] : "K",
        ];

        $response = Http::withHeaders(self::getHeader())->post(config('constant.nsdl.new-pan-url'), $data);
        if ($response->successful()) {
            return $response->json();
        } else {
            return false;
        }
    }

    public static function updatePan($user)
    {
        $data = [
            'name'          => $user['name'],
            'middle_name'   => $user['middle_name'],
            'last_name'     => $user['last_name'],
            'email'         => $user['email'],
            'phone'         => $user['phone'],
            'gender'        => $user['gender'],
            'dob'           => Carbon::parse($user['dob'])->format('Y-m-d'),
            'return_url'    => url(config('constant.nsdl.return_url')),
            'IsPhyPan'      => !empty($user['card_type']) ? $user['card_type'] : "Y",
            'kyc_type'      => !empty($user['kyc_type']) ? $user['kyc_type'] : "K",
        ];

        $response = Http::withHeaders(self::getHeader())->post(config('constant.nsdl.update-pan-url'), $data);
        if ($response->successful()) {
            return $response->json();
        } else {
            return false;
        }
    }

    public static function incomplete($incomplete_txn_id)
    {
        $data = ['txn_id' => $incomplete_txn_id];
        $response = Http::withHeaders(self::getHeader())->post(config('constant.nsdl.incomplete'), $data);
        if ($response->successful()) {
            if ($response->json('status')) {
                return $response->json('data');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function status($ackNo = null)
    {
        $response = Http::withHeaders(self::getHeader())->post(config('constant.nsdl.status-pan'), ['nsdl_ack_no' => $ackNo]);
        if ($response->successful()) {
            if ($response->json('status')) {
                return $response->json('data');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function checkTransStatus($txn_id)
    {
        $response = Http::withHeaders(self::getHeader())->post(config('constant.nsdl.check-trans-status'), ['nsdl_txn_id' => $txn_id]);
        if ($response->successful()) {
            if ($response->json('status')) {
                return $response->json('data');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getInfo($txn_id)
    {
        $response = Http::withHeaders(self::getHeader())->get(config('constant.nsdl.pan-card-info') . '/' . $txn_id);
        if ($response->successful()) {
            if ($response->json('status')) {
                return $response->json('data');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getProfile()
    {
        $response = Http::withHeaders(self::getHeader())->get(config('constant.nsdl.active-services'));
        if ($response->successful()) {
            if ($response->json('status')) {
                return $response->json('data');
            } else {
                return false;
            }
        } else {
            $res = $response->json();
            if ($res['message']) {
                Session::flash('warning', $res['message']);
            }
            return false;
        }
    }
}
