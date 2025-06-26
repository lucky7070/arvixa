<?php

namespace App\Http\Controllers;

use App\Models\Cms;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cms::select('id', 'title', 'image', 'status', 'created_at');

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
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(108, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('cms.edit', $row['id']) . '">Edit</a>';
                    }
                    // if (userCan(108, 'can_delete')) {
                    //     $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    // }

                    if (userAllowed(108)) {
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
        return view('cms.index');
    }

    public function add()
    {
        return view('cms.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        $data = [
            'title'         => $request->title,
            'description'   => $request->description,
            'status'        => $request->status,
            'image'         => 'cms/image.png',
        ];

        $path = 'cms';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        Cms::create($data);
        return redirect(route('cms'))->with('success', 'Cms Added Successfully!!');
    }

    public function edit($id)
    {
        $cms = Cms::firstWhere('id', $id);
        if ($cms == null) {
            return redirect(route('cms'))->with('error', 'Cms Not Found!!');
        }
        return view('cms.edit', compact(['cms']));
    }

    public function update(Request $request, $id)
    {
        $cms = Cms::firstWhere('id', $id);
        if ($cms == null) {
            return redirect(route('cms'))->with('error', 'Cms Not Found!!');
        }

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        $data = [
            'title'         => $request->title,
            'description'   => $request->description,
            'status'        => $request->status
        ];

        $path = 'cms';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']      = $path . '/' . $uploadImage;
        }
        $cms->update($data);
        return redirect(route('cms'))->with('success', 'Cms Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $cms = Cms::where('id', $request->id)->first();
            if ($cms == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Cms Not Found.',
                ]);
            }

            $cms->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Cms deleted Successfully',
            ]);
        }
    }
}
