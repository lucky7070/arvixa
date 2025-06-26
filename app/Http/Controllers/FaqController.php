<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Faq::select('id', 'question', 'answer', 'status', 'created_at');

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('question', function ($row) {
                    return Str::limit($row['question'], 40);
                })
                ->editColumn('answer', function ($row) {
                    return Str::limit($row['answer'], 20);
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(120, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('faq.edit', $row['id']) . '">Edit</a>';
                    }
                    if (userCan(120, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(120)) {
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
        return view('faq.index');
    }

    public function add()
    {
        return view('faq.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'question'      => ['required', 'string', 'max:1000'],
            'answer'        => ['required', 'string']
        ]);

        $data = [
            'question'  => $request->question,
            'answer'    => $request->answer,
            'status'    => $request->status
        ];

        Faq::create($data);
        return redirect(route('faq'))->with('success', 'Faq Added Successfully!!');
    }

    public function edit($id)
    {
        $faq = Faq::firstWhere('id', $id);
        if ($faq == null) {
            return redirect(route('faq'))->with('error', 'Faq Not Found!!');
        }
        return view('faq.edit', compact(['faq']));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::firstWhere('id', $id);
        if ($faq == null) {
            return redirect(route('faq'))->with('error', 'Faq Not Found!!');
        }

        $validated = $request->validate([
            'question'      => ['required', 'string', 'max:1000'],
            'answer'        => ['required', 'string']
        ]);

        $data = [
            'question'      => $request->question,
            'answer'        => $request->answer,
            'status'        => $request->status
        ];
        $faq->update($data);
        return redirect(route('faq'))->with('success', 'Faq Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $faq = Faq::where('id', $request->id)->first();
            if ($faq == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Faq Not Found.',
                ]);
            }

            $faq->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Faq deleted Successfully',
            ]);
        }
    }
}
