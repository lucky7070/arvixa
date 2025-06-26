<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Slider::select('id', 'heading', 'sub_heading', 'image', 'status', 'created_at');

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('sub_heading', function ($row) {
                    return Str::limit($row['sub_heading'], 40);
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(118, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('sliders.edit', $row['id']) . '">Edit</a>';
                    }
                    if (userCan(118, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(118)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('sliders.index');
    }

    public function add()
    {
        return view('sliders.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'heading'       => ['nullable', 'string', 'max:255'],
            'sub_heading'   => ['nullable', 'string', 'max:500'],
            'status'        => ['required', 'integer'],
            'is_special'    => ['required', 'integer'],
            'image'         => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        $data = [
            'heading'       => $request->heading,
            'sub_heading'   => $request->sub_heading,
            'status'        => $request->status,
            'url'           => $request->url,
            'is_special'    => $request->is_special,
            'image'         => 'sliders/image.png',
        ];

        $path = 'sliders';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        Slider::create($data);
        return redirect(route('sliders'))->with('success', 'Slider Added Successfully!!');
    }

    public function edit($id)
    {
        $slider = Slider::firstWhere('id', $id);
        if ($slider == null) {
            return redirect(route('sliders'))->with('error', 'Slider Not Found!!');
        }
        return view('sliders.edit', compact(['slider']));
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::firstWhere('id', $id);
        if ($slider == null) {
            return redirect(route('sliders'))->with('error', 'Slider Not Found!!');
        }

        $validated = [
            'heading'       => ['nullable', 'string', 'max:255'],
            'sub_heading'   => ['nullable', 'string', 'max:500'],
            'status'        => ['required', 'integer'],
            'is_special'    => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ];

        $request->validate($validated);
        $data = [
            'heading'       => $request->heading,
            'sub_heading'   => $request->sub_heading,
            'status'        => $request->status,
            'is_special'    => $request->is_special,
            'url'           => $request->url,
        ];

        $path = 'sliders';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $slider->update($data);
        return redirect(route('sliders'))->with('success', 'Slider Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $slider = Slider::where('id', $request->id)->first();
            if ($slider == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Slider Not Found.',
                ]);
            }

            $slider->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Slider deleted Successfully',
            ]);
        }
    }
}
