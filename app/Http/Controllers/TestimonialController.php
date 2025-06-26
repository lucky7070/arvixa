<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Testimonial::select('id', 'name', 'description', 'image', 'status', 'created_at');

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
                ->editColumn('description', function ($row) {
                    return Str::limit($row['description'], 40);
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(119, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('testimonials.edit', $row['id']) . '">Edit</a>';
                    }
                    if (userCan(119, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(119)) {
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
        return view('testimonials.index');
    }

    public function add()
    {
        return view('testimonials.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'designation'   => ['required', 'string', 'max:255'],
            'status'        => ['required', 'integer'],
            'description'   => ['required', 'string', 'max:1000'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        $data = [
            'name'          => $request->name,
            'designation'   => $request->designation,
            'description'   => $request->description,
            'status'        => $request->status,
            'image'         => 'testimonials/image.png',
        ];

        $path = 'testimonials';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }


        Testimonial::create($data);
        return redirect(route('testimonials'))->with('success', 'Testimonial Added Successfully!!');
    }

    public function edit($id)
    {
        $testimonial = Testimonial::firstWhere('id', $id);
        if ($testimonial == null) {
            return redirect(route('testimonials'))->with('error', 'Testimonial Not Found!!');
        }
        return view('testimonials.edit', compact(['testimonial']));
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::firstWhere('id', $id);
        if ($testimonial == null) {
            return redirect(route('testimonials'))->with('error', 'Testimonial Not Found!!');
        }

        $validated = [
            'name'          => ['required', 'string', 'max:255'],
            'designation'   => ['required', 'string', 'max:255'],
            'status'        => ['required', 'integer'],
            'description'   => ['required', 'string', 'max:1000'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ];

        $request->validate($validated);
        $data = [
            'name'          => $request->name,
            'designation'   => $request->designation,
            'description'   => $request->description,
            'status'        => $request->status
        ];

        $path = 'testimonials';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }



        $testimonial->update($data);
        return redirect(route('testimonials'))->with('success', 'Testimonial Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $testimonial = Testimonial::where('id', $request->id)->first();
            if ($testimonial == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Testimonial Not Found.',
                ]);
            }

            $testimonial->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Testimonial deleted Successfully',
            ]);
        }
    }
}
