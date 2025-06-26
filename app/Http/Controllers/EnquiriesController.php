<?php

namespace App\Http\Controllers;

use App\Models\Enquiries;
use App\Models\JoinRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class EnquiriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Enquiries::select('id', 'name', 'email', 'phone', 'message', 'created_at');

            return Datatables::of($data)->addIndexColumn()

                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('message', function ($row) {
                    return Str::limit($row['message'], 30);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    $btn .= '<button class="dropdown-item view" data-all="' . htmlspecialchars(json_encode($row))  . '">View</button>';
                    if (userCan(121, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(121)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('enquiries.index');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $enquiries = Enquiries::where('id', $request->id)->first();
            if ($enquiries == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Enquiries Not Found.',
                ]);
            }

            $enquiries->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Enquiries deleted Successfully',
            ]);
        }
    }

    public function join_requests(Request $request)
    {
        if ($request->ajax()) {
            $data = JoinRequest::select('id', 'request_for', 'name', 'email', 'phone', 'message', 'created_at');

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('request_for', function ($row) {
                    return $row->request_for;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('message', function ($row) {
                    return Str::limit($row['message'], 30);
                })
                ->addColumn('action', function ($row) {
                    switch ($row->request_for) {
                        case 2:
                            $type = "Main Distributor";
                            break;
                        case 3:
                            $type = "Distributor";
                            break;
                        case 4:
                            $type = "Retailer";
                            break;
                        default:
                            $type = "...";
                            break;
                    }
                    $row->request_for = $type;
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    $btn .= '<button class="dropdown-item view" data-all="' . htmlspecialchars(json_encode($row))  . '">View</button>';
                    if (userCan(121, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(121)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('enquiries.join-requests');
    }

    public function join_request_delete(Request $request)
    {
        if ($request->id) {
            $joinReq = JoinRequest::where('id', $request->id)->first();
            if ($joinReq == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Join Request Not Found.',
                ]);
            }

            $joinReq->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Join Request deleted Successfully',
            ]);
        }
    }
}
