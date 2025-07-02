<?php

use App\Models\ServicesLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

function prd($data = null)
{
    echo "<pre>";
    print_r($data);
    exit;
}

function removeFile($filename = '')
{
    if (in_array($filename, [
        'application/favicon.png',
        'application/logo.png',
        'admin/avatar.png',
        'services/banner.jpg',
        'services/image.png',
    ])) {
        return true;
    }

    if (File::exists(storage_path('app\public\\' . $filename))) {
        File::delete(storage_path('app\public\\' . $filename));
    }
    return true;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz----';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function routeis($expression)
{
    return in_array(Route::currentRouteName(), explode(',', $expression)) ? 'true' : 'false';
}

function getGuardFromURL($request = null, $type = true)
{
    if ($request->is('distributor/*') || $request->is('distributor')) {
        $route = 'distributor';
    } else if ($request->is('main_distributor/*') || $request->is('main_distributor')) {
        $route = 'main_distributor';
    } else if ($request->is('retailer/*') || $request->is('retailer')) {
        $route = 'retailer';
    } else if ($request->is('employee/*') || $request->is('employee')) {
        $route = 'employee';
    } else {
        $route = $type ? 'web' : '';
    }
    return $route;
}

function getTableFromURL($request = null)
{
    if ($request->is('distributor/*') || $request->is('distributor')) {
        $route = 'distributors';
    } else if ($request->is('main_distributor/*') || $request->is('main_distributor')) {
        $route = 'main_distributors';
    } else if ($request->is('retailer/*') || $request->is('retailer')) {
        $route = 'retailers';
    } else if ($request->is('employee/*') || $request->is('employee')) {
        $route = 'employees';
    } else {
        $route = 'users';
    }
    return $route;
}

function checkRoute($route)
{
    if (Route::has(implode('.', array_filter(explode('/', $route))))) {
        return true;
    } else {
        return false;
    }
}

function imageexist($image_path)
{
    if ($image_path == '' || file_exists(base_path('storage/' . $image_path))) {
        return public_path() . '/assets/default/banner.jpg';
    } else {
        return asset('storage/' . $image_path);
    }
}

function orderId($a, $prefix = 'ORD', $len = 10)
{
    $x = $len - (gettype($a) == 'string' ? strlen($a) : strlen((string) $a));
    for ($i = 1; $i <= (int) $x; $i++) {
        $a = "0" . $a;
    }
    return $prefix . $a;
}

function getTransactionDetails($prefix = "", $data = [], $mode = 1)
{
    if ($mode == 1) {
        return $prefix . " " . ($data['payment_type'] == 1 ? 'Credit' : 'Debit') . " : " . $data['particulars'];
    } else {
        return ($data['payment_type'] == 1 ? 'Send To ' : 'Take From ') . $prefix . " : " . $data['particulars'];
    }
}

function userCan($module_id = [], $type = "can_view"): bool
{
    if (gettype($module_id) == 'array') {
        $module = (array) $module_id;
    } else {
        $module = [$module_id];
    }

    $permission = request()->permission->toArray();
    $result = array_filter($permission, function ($item) use ($module, $type) {
        if (in_array($item['module_id'], $module) && ($item['allow_all'] == 1 || $item[$type] == 1)) {
            return $item;
        }
        return false;
    });

    if (count($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function userAllowed($module_id = 0, $type = ['can_edit', 'can_delete'])
{
    $permission = request()->permission->toArray();
    $result = array_filter($permission, function ($item) use ($module_id) {
        if ($item['module_id'] == $module_id) {
            return $item;
        }
        return false;
    });

    if (count($result) > 0) {
        $item = array_values($result)[0];
        $result = array_filter($type, function ($row) use ($item) {
            if (!empty($item[$row])) {
                return $item;
            }
            return false;
        });

        if ($item['allow_all'] == 1  || count($result) > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getDocumentType($id)
{
    $list = config('constant.documents_type_list', []);
    return empty($list[$id]) ? "" : $list[$id];
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function yesNo($check)
{
    return  $check ? 'Yes' : 'No';
}

function makeSlug($str = '')
{
    return Str::slug($str) . '-' . rand(1000, 9999);
}

function checkInRange(int $min = 1, int $max = 100, int|null $inputQty = 1)
{
    if (!in_array($inputQty, range($min, $max))) {
        if ($inputQty <= $min) $inputQty = $min;
        if ($inputQty >= $max) $inputQty = $max;
    }

    return $inputQty;
}

function disabled($check)
{
    return $check ? 'disabled' : "";
}

function getNameFromNumber($num)
{
    $numeric = ($num - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($num - 1) / 26);
    if ($num2 > 0) {
        return getNameFromNumber($num2) . $letter;
    } else {
        return $letter;
    }
}


function uploadfromUrl($url, $path = 'admin')
{
    try {
        $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . pathinfo($url, PATHINFO_EXTENSION);
        Storage::disk('local')->put('public\\' . $path . '/' . $uploadImage, file_get_contents($url));
        return $path . '/' . $uploadImage;
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
        return null;
    }
}

// Function to decrypt hash
function hash_decrypt($hash, $secretKey)
{
    return openssl_decrypt($hash, "AES-128-ECB", $secretKey);
}

function saveFile(UploadedFile|null $image, $folder = 'admin'): null|string
{
    try {
        if ($image) {
            return $image->storeAs($folder, time() . '_' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension());
        } else {
            return null;
        }
    } catch (\Throwable $th) {
        return null;
    }
}
