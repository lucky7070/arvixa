<?php

namespace App\Traits\Mobile;

use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\CartItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Common\StoreController;
use App\Http\Controllers\Common\CommonController;

trait StoreServices
{
    public static function products(Request $request)
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 16);
        $limit  = $limit > 50 ? 50 : $limit;

        $query = Product::query();
        $query->where('status', 1);
        $query->with(['main_image']);

        if (request('category')) $query->where('category_id', request('category'));

        // Search
        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
                $query->orWhere('sort_description', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Ordering
        switch (request('orderAs')) {
            case 1:
                $query->orderBy('price', 'ASC');
                break;
            case 2:
                $query->orderBy('price', 'DESC');
                break;
            case 3:
                $query->orderBy('is_feature', 'DESC');
                break;
            default:
                $query->latest();
                break;
        }

        $total      = $query->count();
        $totalPage  = ceil($total / $limit);

        // Set Offset
        $query->offset($limit * ($pageNo - 1));

        // Limiting
        $query->limit($limit);

        $products   = $query->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                'products'      => $products,
                'pageNo'        => (int) $pageNo,
                'totalPage'     => (int) $totalPage
            ],
        ]);
    }

    public static function categories(Request $request)
    {
        $category = Category::where('status', 1)->where('is_feature', 1)->get();
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $category,
        ]);
    }

    public static function product_details(Request $request, $slug)
    {
        $product = Product::with(['images', 'category', 'brand', 'hsn_code'])
            ->where('status', 1)
            ->where(function ($query) use ($slug) {
                $query->where('id', $slug);
                $query->orWhere('slug', $slug);
            })->first();

        if (!$product) {
            return response()->json([
                'status'    => false,
                'message'   => 'Product Not Found..!!',
                'data'      => '',
            ]);
        }

        $product->category  = CategoryController::getFullName($product->category);
        $product->images    = $product->images->sortBy('sort_order')->values();

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $product,
        ]);
    }

    public function cart(Request $request)
    {
        if ($request->isMethod('get')) {
            $cart = CartItem::with('product')
                ->select('product_id', 'qty')
                ->where('user_id', $this->user_id)
                ->where('user_type',  $this->user_type)
                ->get();

            $cart->filter(fn ($row) => $row->qty > $row->product->stock)->each->delete();
            $cart   = $cart->filter(fn ($row) => $row->qty <= $row->product->stock);

            $cart = $cart->map(function ($row) {
                $amount     = (float) $row->product->price *  (int) $row->qty;
                $tax_amount = $amount * (float) $row->product->hsn_code->tax_rate / ($row->product->hsn_code->tax_rate + 100);

                $row->sub_total  = round($amount - $tax_amount, 2);
                $row->tax        = round($tax_amount, 2);
                $row->total      = round($amount, 2);

                return $row;
            });

            $delivery = StoreController::getShipping($cart, $request);
            $discount = StoreController::getDiscount($cart, $request);
            $summery    = [
                'discount'  => $discount,
                'delivery'  => $delivery,
                'sub_total' => $cart->sum('sub_total'),
                'tax'       => $cart->sum('tax'),
                'total'     => $cart->sum('total') + $delivery - $discount,
            ];


            return response()->json([
                'status'    => true,
                'message'   => "Success",
                "data"      => [
                    'cart'          => $cart,
                    'coupon_code'   => $this->user->voucher_id,
                    'summery'       => $summery
                ]
            ]);
        }

        return StoreController::cart($request);
    }

    public static function vouchers()
    {
        $offers = Voucher::where('status', 1)
            ->select('code', 'name', 'description', 'is_free_shipping', 'starts_at', 'expires_at')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->where('is_public', 1)
            ->get();

        return response()->json([
            'status'    => true,
            'message'   => "Success",
            "data"      => $offers
        ]);
    }

    public function apply_voucher(Request $request)
    {
        $code = $request->code;
        $cart = CartItem::with('product')->where('user_id', $this->user_id)->where('user_type',  $this->user_type)->get();
        $check = StoreController::checkVoucher($code, $cart);
        return response()->json($check);
    }

    public function remove_voucher()
    {
        return StoreController::remove_voucher();
    }

    public function placeOrder(Request $request)
    {
        return StoreController::placeOrder($request);
    }

    public function cancel_order(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'order_id'     => ['required'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        }

        return StoreController::cancel_order($request);
    }

    public function my_orders(Request $request)
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 10);
        $limit  = $limit > 50 ? 50 : $limit;

        $query = Order::query();
        $query->with(['products', 'history:comment,date,order_id,order_status_id']);
        $query->withCasts(['date' => 'datetime:d F, Y']);
        $query->where('user_id', $this->user_id);
        $query->where('user_type',  $this->user_type);

        // Search
        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('voucher_no', 'LIKE', "%{$search}%");
                $query->orWhere('customer_name_1', 'LIKE', "%{$search}%");
                $query->orWhere('customer_name_2', 'LIKE', "%{$search}%");
                $query->orWhere('customer_email', 'LIKE', "%{$search}%");
                $query->orWhere('customer_mobile', 'LIKE', "%{$search}%");
                $query->orWhere('shipping_address_1', 'LIKE', "%{$search}%");
                $query->orWhere('shipping_address_2', 'LIKE', "%{$search}%");
                $query->orWhere('shipping_city', 'LIKE', "%{$search}%");
                $query->orWhere('shipping_state', 'LIKE', "%{$search}%");
                $query->orWhere('shipping_postcode', 'LIKE', "%{$search}%");
            });
        }

        $total      = $query->count();
        $totalPage  = ceil($total / $limit);

        // Set Offset
        $query->offset($limit * ($pageNo - 1));

        // Limiting
        $query->limit($limit);

        $query->latest();

        $data = $query->get()->append('order_status_class');
        return response()->json([
            'status'    => true,
            'message'   => 'Success..!!',
            'data'      => $data,
            'totalPage' => $totalPage
        ]);
    }
}
