<?php

namespace App\Http\Controllers;

use App\Models\BannerAdmin;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BannerAdmin::select('*');
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';

                    if (userCan(113, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                    }

                    if (userCan(113, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(113)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->editColumn('image', function ($row) {
                    return '<img src="' . asset('storage/' . $row['image']) . '" class="img-thumbnail" style="height: 80px; max-width : 400px" alt="' . $row['image'] . '" srcset="">';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d F, Y');
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
        return view('banners.index');
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image'         => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:2048', 'dimensions:width=1024,height=175'],
            'banner_for'    => ['required', 'array'],
            'url'           => ['nullable', 'string', 'url'],
            'is_special'    => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $err = array();
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validation->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {

            $path = 'banners';
            if ($file = $request->file('image')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));

                BannerAdmin::create([
                    'banner_for'    => implode(',', $request->banner_for),
                    'url'           => $request->url,
                    'is_special'    => $request->is_special,
                    'image'         => $path . '/' . $uploadImage,
                    'status'        => 1
                ]);
            }

            return response()->json([
                'success'   => true,
                'message'   => 'Banner Added Successfully',
                'data'      => ''
            ]);
        }
    }

    public function update(Request $request)
    {
        $slider = BannerAdmin::firstWhere('id', $request->id);
        if ($slider == null) {
            return response()->json([
                'success'   => true,
                'message'   => 'Banner Not Found!!',
                'data'      => ''
            ]);
        }

        $validated = [
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'banner_for'    => ['required', 'array'],
            'url'           => ['nullable', 'string'],
            'is_special'    => ['required', 'integer'],
        ];

        $request->validate($validated);
        $data = [
            'banner_for'    => implode(',', $request->banner_for),
            'url'           => $request->url,
            'is_special'    => $request->is_special,
            'status'        => 1
        ];

        $path = 'sliders';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $slider->update($data);
        return response()->json([
            'success'   => true,
            'message'   => 'Banner Updated Successfully..!!',
            'data'      => ''
        ]);
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $state = BannerAdmin::where('id', $request->id)->first();
            if ($state == null) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Banner Not Found.',
                ]);
            }

            $state->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Banner deleted Successfully',
            ]);
        }
    }
}
