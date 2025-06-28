<?php

namespace App\Http\Controllers\Retailer;

use App\Models\PanCard;
use App\Models\Customer;
use App\Models\Services;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Library\PanCard as LibraryPanCard;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\Common\LedgerController;

class PanCardController extends Controller
{
    protected $user_id      = null;
    protected $user_type    = null;

    public function __construct()
    {
        $this->middleware(['auth:retailer', function ($request, $next) {
            $this->user_id      = auth()->guard('retailer')->id();
            return $next($request);
        }])->except(['change_pan_card_status', 'pan_card_status']);

        $this->user_type    = 4;
    }

    public function pan_card(Request $request, $card_type = 'physical')
    {
        $service_id = $card_type == 'digital' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        $service = Services::find($service_id);

        $is_physical_card = $card_type == 'digital' ? 'N' : 'Y';
        if ($request->ajax()) {
            $data = PanCard::select('id', 'name', 'type', 'middle_name', 'is_refunded', 'last_name', 'email', 'slug', 'phone', 'doc', 'nsdl_ack_no', 'nsdl_txn_id', 'nsdl_complete', 'created_at')
                ->where('is_physical_card', $is_physical_card)
                ->where('user_type', $this->user_type)
                ->where('user_id', $this->user_id);

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('nsdl_complete', function ($row) {
                    $status = '';
                    if ($row['nsdl_complete'] == 1 && $row['nsdl_ack_no'] != null) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Submitted</small>';
                    } elseif ($row['nsdl_complete'] == 1 && $row['is_refunded'] == 1) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-dark"> Not Submitted</small>';
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Refunded</small>';
                    } else {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> InComplete</small>';
                    }

                    return $status;
                })
                ->editColumn('nsdl_txn_id', function ($row) {
                    return '<b>' . $row['nsdl_txn_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('type', function ($row) {
                    return $row['type'] == 1 ? '<span class="badge badge-light-primary">New</span>' : '<span class="badge badge-light-secondary">Update</span>';
                })
                ->addColumn('full_name', function ($row) {
                    return '<b>' . trim($row->fname . " " . $row->middle_name . " " . $row->last_name) . '</b><br /> <span>(' . $row->phone . ')</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-outline-dark checkStatus p-1" data-ack-no="' . $row['nsdl_ack_no'] . '" data-txn-id="' . $row['nsdl_txn_id'] . '">Check Status</button>';
                    // if ($row['nsdl_complete'] == 1) {
                    // } else {
                    //     $btn = '<button class="btn btn-outline-primary complatePan p-1" data-txn-id="' . $row['nsdl_txn_id'] . '">Complete</button>';
                    // }
                    return $btn;
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw('CONCAT(name, " ", middle_name, " ", last_name) like ?', ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'nsdl_complete', 'nsdl_txn_id', 'full_name', 'type'])
                ->make(true);
        }
        return view('my_services.pan-card.index', compact('card_type', 'service'));
    }

    public function create_pan_card($card_type = 'physical')
    {
        $service_id = $card_type == 'digital' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");

        if ($serviceLog->sale_rate > auth()->guard('retailer')->user()->user_balance)
            return redirect(route('pan-card'))->with('error', "Insufficient Balance to use this service..!!");

        return view('my_services.pan-card.create_pan_card');
    }

    public function pan_card_save(Request $request, $card_type = 'physical')
    {
        $service_id = $card_type == 'digital' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");

        if ($serviceLog->sale_rate > auth()->guard('retailer')->user()->user_balance)
            return redirect(route('pan-card'))->with('error', "Insufficient Balance to use this service..!!");

        $validated = $request->validate([
            'name'          => ['nullable', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'phone'         => ['required', 'digits:10', 'regex:' . config('constant.phoneRegExp')],
            'gender'        => ['required', 'string'],
            'dob'           => ['required', 'date', 'date_format:Y-m-d', 'before:1 years ago'],
        ]);

        $user = [
            'last_name'     => $request->last_name,
            'name'          => $request->name,
            'middle_name'   => $request->middle_name,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'kyc_type'      => $request->kyc_type,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'card_type'     => $card_type == 'digital' ? 'N' : 'Y'
        ];

        $res = LibraryPanCard::newPan($user);
        if ($res) {

            if ($res['status']) {
                $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
                $customer->first_name    = request('name');
                $customer->middle_name   = request('middle_name');
                $customer->last_name     = request('last_name');
                $customer->dob           = Carbon::parse(request('dob'))->format('Y-m-d');
                $customer->email         = request('email');
                $customer->gender        = request('gender') == 'M' ? 1 : (request('gender') == 'F' ? 2 : (request('gender') == 'T' ? 3 : null));
                $customer->save();

                $apiData    = $res['data'];
                $data       = [
                    'slug'              => Str::uuid(),
                    'type'              => 1,
                    'user_id'           => $this->user_id,
                    'user_type'         => $this->user_type,
                    'name'              => $request->name,
                    'middle_name'       => $request->middle_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                    'gender'            => $request->gender,
                    'doc'               => '',
                    'is_physical_card'  => $card_type == 'digital' ? 'N' : 'Y',
                    'customer_id'       => $customer->id,
                    'nsdl_formdata'     => $apiData['req']['reqEntityData']['formData'],
                    'nsdl_txn_id'       => $apiData['req']['reqEntityData']['txnid']
                ];

                $pan_card = PanCard::create($data);
                ServiceUsesLog::create([
                    'user_id'                       => $this->user_id,
                    'user_type'                     => $this->user_type,
                    'customer_id'                   => $customer->id,
                    'service_id'                    => $service_id,
                    'request_id'                    => $pan_card->id,
                    'used_in'                       => 1,
                    'purchase_rate'                 => $serviceLog->purchase_rate,
                    'sale_rate'                     => $serviceLog->sale_rate,
                    'main_distributor_id'           => $serviceLog->main_distributor_id,
                    'distributor_id'                => $serviceLog->distributor_id,
                    'main_distributor_commission'   => $serviceLog->main_distributor_commission,
                    'distributor_commission'        => $serviceLog->distributor_commission,
                    'is_refunded'                   => 0,
                    'created_at'                    => Carbon::now(),
                ]);

                $submit_url     = $apiData['submit_url'];
                $requestData    = $apiData['req'];
                LedgerController::chargePanCardService($pan_card, $serviceLog);

                return view('my_services.pan-card.confirm', compact('requestData', 'pan_card', 'submit_url'));
            } else {
                $apiData = $res['data'];
                return back()->withInput()->with('error', "validation error.");
            }
        } else {
            return back()->withInput()->with('error', "Oops.. There is Some error.");
        }
    }

    public function update_pan_card($card_type = 'physical')
    {
        $service_id = $card_type == 'digital' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");

        if ($serviceLog->sale_rate > auth()->guard('retailer')->user()->user_balance)
            return redirect(route('pan-card'))->with('error', "Insufficient Balance to use this service..!!");

        return view('my_services.pan-card.update_pan_card');
    }

    public function update_pan_card_save(Request $request, $card_type = 'physical')
    {
        $service_id = $card_type == 'digital' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");

        if ($serviceLog->sale_rate > auth()->guard('retailer')->user()->user_balance)
            return redirect(route('pan-card'))->with('error', "Insufficient Balance to use this service..!!");

        $validated = $request->validate([
            'name'          => ['nullable', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'phone'         => ['required', 'digits:10', 'regex:' . config('constant.phoneRegExp')],
            'gender'        => ['required', 'string'],
            'dob'           => ['required', 'date', 'date_format:Y-m-d', 'before:1 years ago'],
        ]);

        $user = [
            'last_name'     => $request->last_name,
            'name'          => $request->name,
            'middle_name'   => $request->middle_name,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'card_type'     => $card_type == 'digital' ? 'N' : 'Y'
        ];

        $res = LibraryPanCard::updatePan($user);
        if ($res) {

            if ($res['status']) {
                $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
                $customer->first_name    = request('name');
                $customer->middle_name   = request('middle_name');
                $customer->last_name     = request('last_name');
                $customer->dob           = request('dob');
                $customer->email         = request('email');
                $customer->gender        = request('gender') == 'M' ? 1 : (request('gender') == 'F' ? 2 : (request('gender') == 'T' ? 3 : null));
                $customer->save();

                $apiData = $res['data'];
                $data = [
                    'slug'              => Str::uuid(),
                    'type'              => 2,
                    'user_id'           => $this->user_id,
                    'user_type'         => $this->user_type,
                    'name'              => $request->name,
                    'middle_name'       => $request->middle_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                    'gender'            => $request->gender,
                    'doc'               => '',
                    'is_physical_card'  => $card_type == 'digital' ? 'N' : 'Y',
                    'customer_id'       => $customer->id,
                    'nsdl_formdata'     => $apiData['req']['reqEntityData']['formData'],
                    'nsdl_txn_id'       => $apiData['req']['reqEntityData']['txnid']
                ];

                $pan_card = PanCard::create($data);
                ServiceUsesLog::create([
                    'user_id'                       => $this->user_id,
                    'user_type'                     => $this->user_type,
                    'customer_id'                   => $customer->id,
                    'service_id'                    => $service_id,
                    'request_id'                    => $pan_card->id,
                    'used_in'                       => 1,
                    'purchase_rate'                 => $serviceLog->purchase_rate,
                    'sale_rate'                     => $serviceLog->sale_rate,
                    'main_distributor_id'           => $serviceLog->main_distributor_id,
                    'distributor_id'                => $serviceLog->distributor_id,
                    'main_distributor_commission'   => $serviceLog->main_distributor_commission,
                    'distributor_commission'        => $serviceLog->distributor_commission,
                    'is_refunded'                   => 0,
                    'created_at'                    => Carbon::now(),
                ]);

                $submit_url     = $apiData['submit_url'];
                $requestData    = $apiData['req'];
                LedgerController::chargePanCardService($pan_card, $serviceLog);

                return view('my_services.pan-card.confirm', compact('requestData', 'pan_card', 'submit_url'));
            } else {
                $apiData = $res['data'];
                return back()->withInput()->with('error', "validation error.");
            }
        } else {
            return back()->withInput()->with('error', "Oops.. There is Some error.");
        }
    }

    public function incomplete_pan_card(Request $request)
    {
        $txn_id     = $request->txn_id;
        if (!$txn_id)
            return redirect(route('pan-card'))->with('error', "Please provide txn_id.");

        $pan_card   = PanCard::where('nsdl_txn_id', $txn_id)
            ->where('user_id', $this->user_id)
            ->where('user_type',  $this->user_type)
            ->first();

        if (!$pan_card)
            return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");

        if ($pan_card->nsdl_complete != 0) {
            return redirect(route('pan-card'))->with('error',  "Service already Completed..!!");
        }

        $status = LibraryPanCard::checkTransStatus($pan_card->nsdl_txn_id);
        if ($pan_card->type == 1) {
            $service_id = $pan_card->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        } else {
            $service_id = $pan_card->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');
        }

        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if ($status && $status['status'] == 'incomplete') {
            if ($pan_card->is_refunded == 1) {
                if (!$serviceLog) {
                    return redirect(route('pan-card'))->with('error', "Service Can't be used..!!");
                }

                if ($serviceLog->sale_rate > auth()->guard('retailer')->user()->user_balance) {
                    return redirect(route('pan-card'))->with('error', "Insufficient Balance to use this service..!!");
                }
            }

            // Generate PanCard Data
            if ($apiData = LibraryPanCard::incomplete($txn_id)) {
                if ($pan_card->is_refunded == 1) {
                    LedgerController::chargePanCardService($pan_card, $serviceLog);
                }

                $submit_url     = $apiData['submit_url'];
                $requestData    = $apiData['req'];
                return view('my_services.pan-card.confirm', compact('requestData', 'pan_card', 'submit_url'));
            } else {
                return redirect(route('pan-card'))->with('error', "Oops.. There is some error.!!");
            }
        } else {
            if ($pan_card->is_refunded == 0 && $pan_card->nsdl_complete == 0) {

                // Successfully Completed Case
                if ($status && $status['status'] == 'success' && $status['error'] == null) {
                    $pan_card->update([
                        'nsdl_ack_no'       => $status['ack_No'],
                        'nsdl_complete'     => 1,
                        'error_message'     => 'PanCard Request Submitted Successfully..!!'
                    ]);
                }

                // Refund Case
                if ($status && $status['status'] == 'error' || $status['status'] == 'failure' || ($status['status'] == null && $status['error'] != null)) {
                    if ($serviceLog) {
                        $serviceLog = ServiceUsesLog::where([
                            'user_id'       => $this->user_id,
                            'user_type'     => $this->user_type,
                            'service_id'    => $service_id,
                            'request_id'    => $pan_card->id,
                        ])->first();

                        LedgerController::panCardRefund($pan_card, $serviceLog, json_encode($status['error']));
                    }
                }
            }

            return redirect(route('pan-card'))->with('error', "This Pan Transaction id is completed, We can't continue with that.!!");
        }
    }

    public function change_pan_card_status(Request $request)
    {
        $response = json_decode(base64_decode($request->data), true);

        $txnStatus  = @$response['status'];
        $txnid      = @$response['txnid'];
        $ackNo      = @$response['data']['ackNo'];
        $errorMsg   = @$response['message'];

        $pan_card = PanCard::where('nsdl_txn_id', $txnid)->first();
        if (!$pan_card) {
            return redirect(route('pan-card'))->with('success', 'Invalid Request..!!');
        }

        if ($pan_card->type == 1) {
            $service_id = $pan_card->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        } else {
            $service_id = $pan_card->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');
        }

        $serviceLog = ServiceUsesLog::where([
            'user_id'       => $pan_card->user_id,
            'user_type'     => $pan_card->user_type,
            'service_id'    => $service_id,
            'request_id'    => $pan_card->id,
        ])->first();

        if ($txnStatus) {
            if ($pan_card->nsdl_complete == 1 && $pan_card->is_refunded == 1) {
                LedgerController::chargePanCardService($pan_card, $serviceLog);
                $pan_card->update(['nsdl_ack_no' => $ackNo, 'is_refunded' => 0]);
            } else {
                $pan_card->update(['nsdl_ack_no' => $ackNo, 'nsdl_complete' => 1]);
            }

            return redirect(route('pan-card'))->with('success', 'PanCard Request Submitted Successfully..!!');
        }

        $errorMsg = !empty($errorMsg) ? $errorMsg : "";
        LedgerController::panCardRefund($pan_card, $serviceLog, $errorMsg);
        return redirect(route('pan-card'))->with('error', $errorMsg);
    }

    public static function pan_card_status(Request $request)
    {
        if ($ackNo = $request->ackNo) {
            $pan_card = PanCard::firstWhere('nsdl_ack_no', $request->ackNo);
            $output = LibraryPanCard::status($ackNo);
            if (!$output) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Oops.. There is some error.",
                    'data'      => ""
                ]);
            } else {
                $message = 'Oops.. There is some error.';
                if (!empty($output['panStatus']) && !empty($output['pan'])) {
                    $message = $output['panStatus'] . "\n";
                    $message .= 'Your Pan Card Number is : ' . $output['pan'];
                }

                if (!empty($output['panStatus']) && empty($output['pan']))  $message = $output['panStatus'];
                if (!empty($output['error']))  $message = $output['error'];
                if (gettype($output['error']) == 'array' && count($output['error']))  $message = implode(',', array_values($output['error']));

                return response()->json([
                    'status'    => true,
                    'message'   => $message,
                    'data'      => $output
                ]);
            }
        }

        if ($txnId = $request->txnId) {
            $pan_card = PanCard::firstWhere('nsdl_txn_id', $request->txnId);
            $output = LibraryPanCard::checkTransStatus($txnId);
            if (!$output) {
                return response()->json([
                    'success'   => false,
                    'message'   => "Oops.. There is some error.",
                    'data'      => ""
                ]);
            } else {
                $message = 'Oops.. There is some error.';
                if ($output['status'] == 'failure' && $output['errordesc'])  $message = $output['errordesc'];
                elseif ($output['status'] == 'incomplete' && $output['errordesc']) $message = $output['errordesc'];
                elseif ($output['status'] == 'incomplete' && $output['errordesc']) $message = $output['errordesc'];
                elseif ($output['status'] == 'AUTO-CLOSED' && $output['errordesc']) $message = $output['errordesc'];
                elseif ($output['status'] == 'success') {
                    $pan_card->update(['nsdl_ack_no' => $output['ack_No']]);
                    $message = 'Your Application Successfully Submitted. Your Ack no. - ' . $output['ack_No'];
                } elseif (gettype($output['error']) == 'array' && count($output['error']))  $message = implode(',', array_values($output['error']));

                return response()->json([
                    'success'   => true,
                    'message'   => $message,
                    'data'      => $output
                ]);
            }
        }

        return response()->json([
            'success'   => false,
            'message'   => 'Please provide Trans Id or Acknowledge Number.!!',
        ]);
    }

    public function export($card_type = 'physical')
    {
        $is_physical_card = $card_type == 'digital' ? 'N' : 'Y';
        $data = PanCard::select('*')
            ->where('is_physical_card', $is_physical_card)
            ->where('user_type', $this->user_type)
            ->where('user_id', $this->user_id);

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PanCard List');

        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Mobile');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'PanCard TXN Id');
        $sheet->setCellValue('F1', 'PanCard Acknowledgement');
        $sheet->setCellValue('G1', 'Use Type');
        $sheet->setCellValue('H1', 'Physical Card');
        $sheet->setCellValue('I1', 'Completed');
        $sheet->setCellValue('J1', 'Is Refunded');

        $rows = 2;
        foreach ($data->get() as $row) {
            $sheet->setCellValue('A' . $rows, Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('B' . $rows, trim($row->name . ' ' . $row->middle_name . '' . $row->last_name));
            $sheet->setCellValue('C' . $rows, $row->phone);
            $sheet->setCellValue('D' . $rows, $row->email);
            $sheet->setCellValue('E' . $rows, $row->nsdl_txn_id);
            $sheet->setCellValue('F' . $rows, $row->nsdl_ack_no);
            $sheet->setCellValue('G' . $rows, $row->type == 1 ? 'New' : 'Correction');
            $sheet->setCellValue('H' . $rows, $row->is_physical_card == 'Y' ? 'Yes' : 'No');
            $sheet->setCellValue('I' . $rows, $row->nsdl_complete == 1 ? 'Yes' : 'No');
            $sheet->setCellValue('J' . $rows, $row->is_refunded == 1 ? 'Yes' : 'No');
            $rows++;
        }

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            if ($column->getColumnIndex() != 'K')
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            else
                $sheet->getColumnDimension('K')->setWidth(20);
        }

        $sheet->getStyle('A1:A' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('A1:J' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D1:F' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('A1');

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "PanCards Retailer Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
