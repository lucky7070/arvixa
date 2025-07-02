<?php

return [

    // For Pan Card Config
    'nsdl' => [
        'return_url'                => '/change-pan-card-status',
        'refund_after_time_minutes' => 60,
        'x_api_key'                 => 'J2zsA2xVc2RXrQJ0X7TVeLa6Krx82Ig9LFxKezwYYZFXK3V9afgLsV5SkTsuCG4wsupPwTrF7aF4vtdWSQ3uRmGMpxPKCqYKRCDytyzNto39HqWpCp86WQkGPsO9H9k3c8hyRhE6jmGktNifBqcqkuUiA7wyGxthglMIPGrvzy9NCYfv1WtD1rlJE18AU9GBRWyb6F3e7nxsibK6GhWdMVYmJwrFBsiasc6Cf66bqJ6fsJiyID1brWd4s9',
        'x_api_username'            => 'arvixa',

        // *****************************  Api URLs  ******************************************************
        'new-pan-url'               => 'https://adiyogifintech.com/app/api/create-pan',
        'update-pan-url'            => 'https://adiyogifintech.com/app/api/update-pan',
        'incomplete'                => 'https://adiyogifintech.com/app/api/incomplete',
        'pan-card-info'             => 'https://adiyogifintech.com/app/api/pan-card-info',
        'check-trans-status'        => 'https://adiyogifintech.com/app/api/check-trans-status',
        'status-pan'                => 'https://adiyogifintech.com/app/api/status-pan',
        'active-services'           => 'https://adiyogifintech.com/app/api/active-services',
    ],

    // Service Ids
    'service_ids'   => [
        'pan_cards_add'             => 1,
        'pan_cards_edit'            => 1,
        'pan_cards_add_digital'     => 2,
        'pan_cards_edit_digital'    => 2,
        'income_tax_return'          => 3,
        // 'msme_certificate'          => 3,

        'water_bill'                => 7,
        'electricity_bill'          => 8,
        'lic_premium'               => 9,
        'gas_payment'               => 10
    ],

    'front_url'     => 'https://pan.arvixa.in/',
    'secret_token'  => '9906bf6e010b53816cf0684eead09923b051c75ca0b5ad6b9e79dec56',
    'mplan_key'     => env('MPLAN_KEY', "ba0fa41bee5146ebe30f8f7e3c10c68b"),

    'phoneRegExp'       => "/^(?:(?:\+|0{0,2})91(\s*|[-])?|[0]?)?([6789]\d{2}([ -]?)\d{3}([ -]?)\d{4})$/",
    'emailRegExp'       => '/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i',
    'aadhaarRegExp'     => "/^[2-9]{1}[0-9]{3}[0-9]{4}[0-9]{4}$/",
    'pancardRegExp'     => "/[A-Z]{5}[0-9]{4}[A-Z]{1}$/",


    // Firebase Settings
    'fcm_url'       => env('FCM_URL', 'https://fcm.googleapis.com/fcm/send'),
    'firebase_key'  => env('FCM_SERVER_KEY', null),


    'setting_array' => [
        '1' => 'General Settings',
        '2' => 'Social Links Setting',
        '3' => 'Mail Setting',
        '4' => 'Payment Setting',
        '5' => 'SMS Setting',
        '6' => 'Application Setting',
        '7' => 'Mobile App',
    ],

    // Gender List for select gender
    'gender_list'   => [
        '1' => 'Male',
        '2' => 'Female',
        '3' => 'Transgender'
    ],

    // Document Type List for select documents
    'documents_type_list' => [
        '1'     => 'Aadhaar Card',
        '2'     => 'Indian Passport',
        '3'     => 'Voter ID Card',
        '4'     => 'PAN Card',
        '5'     => 'Driving License India',
        '6'     => 'Ration Card',
        '7'     => 'Birth Certificate',
        '8'     => 'SC, ST, OBC Certificate',
        '9'     => 'Post Office Passbooks',
        '10'    => 'Bank Passbooks',
        '11'    => 'Marriage Certificate',
    ],


    'designation_list' => [
        '1'     => 'Customer Support',
    ],

    'user_type_list' => [
        '1'     => 'Admin',
        '2'     => 'Main Distributor',
        '3'     => 'Distributor',
        '4'     => 'Retailer',
        '5'     => 'Employee',
    ],


    // Social Category List for select
    'social_category_list'   => [
        '1' => 'General',
        '2' => 'OBC',
        '3' => 'ST',
        '4' => 'SC',
        '5' => 'Other'
    ],

    // Social Category List for select
    'pancard_type_list'   => [
        "1" => "Individual",
        "2" => "Association Of Persons",
        "3" => "Body Of Persons",
        "4" => "Trust",
        "5" => "Firm",
        "6" => "Limited Liability Partnership",
    ],

    'bank_account_type'         => [
        "1" => "Saving Account",
        "2" => "Current Account",
    ],

    'bank_account_holder_type'  => [
        "1" => "Individual Account",
        "2" => "Joint Account",
    ],

    'employeer_types'    => [
        "1" => "Private",
        "2" => "Central Government",
        "3" => "State Government",
        "4" => "Puboptionc Sector Unit",
        "5" => "Pensioners - Central Government",
        "6" => "Pensioners - State Government",
        "7" => "Pensioners - Puboptionc Sector Undertaking",
        "8" => "Pensioners - Others",
    ],

    'rented_house_type'  => [
        "1" => "Self Occupied",
        "2" => "Rented",
        "3" => "Deemed Let Out",
    ],

    'capital_gain_asset_type' => [
        "1" => "Mutual Funds",
        "2" => "Shares",
        "3" => "Property",
        "4" => "House",
        "5" => "Land",
        "6" => "Building",
        "7" => "Other",
    ],

    'business_type_list' => [
        "1" => "Manufacturing",
        "2" => "Trading",
        "3" => "Service",
    ],

    'itr_file_status_list' => [
        '0'     => 'Pending',
        '1'     => 'Submitted',
        '2'     => 'Completed',
        '3'     => 'Rejected',
        '4'     => 'Under Draft',
    ],









];
