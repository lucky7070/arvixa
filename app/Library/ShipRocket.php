<?php

namespace App\Library;

use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShipRocket
{
    public static function createToken($settings)
    {
        try {
            // Send the GET request with cURL
            $response = Http::post($settings['shiprocket_url'] . '/auth/login', [
                'email'    => $settings['shiprocket_email'],
                'password' => $settings['shiprocket_password'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'];
            } else {
                Log::error("ShipRocket Error Auth:: " . " -- " . $response->body());
                return null;
            }
        } catch (\Throwable $th) {
            Log::error("ShipRocket Error :: " . $e->getMessage());
            return null;
        }
    }

    public static function createOrder($settings, Order $order, $weight = 0.5)
    {
        try {
            if ($settings['shiprocket_toggle'] == 0) {
                return false;
            }

            // Generate Auth Token
            $token = self::createToken($settings);

            // Prepare order products data
            $orderProducts = $order->products->map(function ($product) {

                // $tax_amount = (float) $product['unit_price'] * (float) $product['tax_rate'] / ((float) $product['tax_rate'] + 100);
                return [
                    "name"              => $product['product_name'],
                    "sku"               => $product['product_sku'],
                    "units"             => $product['quantity'],
                    "selling_price"     => $product['unit_price'],
                    "discount"          => 0,
                    "tax"               => $product['tax_rate'],
                    "hsn"               => $product['hsn_code']
                ];
            });

            $requestData = [
                "order_id"                  => $order->voucher_no,
                "order_date"                => Carbon::now()->format('Y-m-d H:i'),
                "pickup_location"           => "OFFICE",
                "channel_id"                => $settings['shiprocket_channel_id'],
                "comment"                   => "Reseller: ",
                "company_name"              => $settings['application_name'],
                "billing_customer_name"     => $order->customer_name_1,
                "billing_last_name"         => $order->customer_name_2,
                "billing_address"           => $order->shipping_address_1,
                "billing_address_2"         => $order->shipping_address_2,
                "billing_city"              => $order->shipping_city,
                "billing_state"             => $order->shipping_state,
                "billing_pincode"           => $order->shipping_postcode,
                "billing_country"           => "India",
                "billing_email"             => $order->customer_email,
                "billing_phone"             => $order->customer_mobile,
                "shipping_is_billing"       => true,
                "shipping_customer_name"    => "",
                "shipping_last_name"        => "",
                "shipping_address"          => "",
                "shipping_address_2"        => "",
                "shipping_city"             => "",
                "shipping_pincode"          => "",
                "shipping_country"          => "",
                "shipping_state"            => "",
                "shipping_email"            => "",
                "shipping_phone"            => "",
                "order_items"               => $orderProducts->toArray(),
                "payment_method"            => "Prepaid",
                "shipping_charges"          => $order->delivery,
                "giftwrap_charges"          => 0,
                "transaction_charges"       => 0,
                "total_discount"            => $order->discount,
                "sub_total"                 => $order->sub_total + $order->tax,
                "length"                    => 10,
                "breadth"                   => 10,
                "height"                    => 10,
                "weight"                    => $weight
            ];

            // Send the GET request with cURL
            $response = Http::withToken($token)->post($settings['shiprocket_url'] . '/orders/create/adhoc', $requestData);
            if ($response->ok()) {
                $data = $response->json();
                return $data;
            } else {
                Log::error("ShipRocket Error for order :: " . $order->voucher_no . " -- " . $response->body());
                return false;
            }
        } catch (Exception $e) {

            Log::error("ShipRocket Error :: " . $e->getMessage() . json_encode($order->toArray()));
            return false;
        }
    }

    public static function cancelOrder($settings, Order $order)
    {
        try {

            if ($order->shiprocket_order_id == null) {
                return false;
            }

            // Generate Auth Token
            $token = self::createToken($settings);

            // Send the GET request with cURL
            $response = Http::withToken($token)->post($settings['shiprocket_url'] . '/orders/cancel', ['ids' => [$order->shiprocket_order_id]]);
            if ($response->ok()) {
                $data = $response->json();
                return $data;
            } else {
                Log::error("ShipRocket Error for order :: " . $order->voucher_no . " -- " . $response->body());
                return false;
            }
        } catch (Exception $e) {
            Log::error("ShipRocket Error :: " . $e->getMessage() . json_encode($order->toArray()));
            return false;
        }
    }
}
