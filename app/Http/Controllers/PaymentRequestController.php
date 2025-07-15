<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use App\Models\Distributor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use Illuminate\Support\Carbon;
use App\Models\MainDistributor;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\Common\LedgerController;
use Illuminate\Support\Facades\DB;

class PaymentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function payment_request(Request $request)
    {
        if ($request->ajax()) {
            $query = PaymentRequest::query();
            $query->select(
                'payment_requests.id',
                'user_type',
                'request_number',
                'amount',
                'title',
                'reason',
                'description',
                'attachment',
                'payment_requests.status',
                'payment_requests.updated_at',
                'main_distributors.name as m_name',
                'distributors.name as d_name',
                'retailers.name as r_name',
                'main_distributors.mobile as m_mobile',
                'distributors.mobile as d_mobile',
                'retailers.mobile as r_mobile',
                'payment_modes.name as payment_mode_name'
            );

            $query->join('payment_modes', 'payment_modes.id', 'payment_requests.payment_mode_id');
            $query->leftJoin('main_distributors', function ($join) {
                $join->on('main_distributors.id', '=', 'payment_requests.user_id');
                $join->where('user_type', 2);
            });
            $query->leftJoin('distributors', function ($join) {
                $join->on('distributors.id', '=', 'payment_requests.user_id');
                $join->where('user_type', 3);
            });
            $query->leftJoin('retailers', function ($join) {
                $join->on('retailers.id', '=', 'payment_requests.user_id');
                $join->where('user_type', 4);
            });

            if (request('start_date') && request('end_date')) {
                if (request('start_date') == request('end_date')) {
                    $query->whereDate('payment_requests.updated_at', request('start_date'));
                } else {
                    if (request('start_date') && request('end_date')) {
                        $startDate = Carbon::parse(request('start_date'));
                        $endDate = Carbon::parse(request('end_date'))->endOfDay();
                        $query->whereBetween('payment_requests.updated_at', [$startDate, $endDate]);
                    }
                }
            }

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('request_number', function ($row) {
                    $role = '';
                    $color  = 'text-dark';
                    if ($row['user_type'] == 2) {
                        $role   = 'Main Distributors';
                        $color  = 'text-secondary';
                    }

                    if ($row['user_type'] == 3) {
                        $role   = 'Distributors';
                        $color  = 'text-warning';
                    }

                    if ($row['user_type'] == 4) {
                        $role   = 'Retailer';
                        $color  = 'text-danger';
                    }

                    return '<span data-data="' . htmlentities(json_encode($row)) . '" class="fw-bold text-primary viewDetails pointer">' . $row['request_number'] . '</span><br><span class="mb-0 prd-category ' . $color . ' fs--1">' . $role . '</span>';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row['updated_at'] ? $row['updated_at']->format('d M, Y') : '';
                })
                ->editColumn('title', function ($row) {
                    return Str::limit($row['title'], 30, '...');
                })
                ->addColumn('user_name', function ($row) {
                    $name = 'n/a';
                    $role = '';
                    $color  = 'text-dark';
                    if ($row['user_type'] == 2) {
                        $name       = $row['m_name'];
                        $mobile     = $row['m_mobile'];
                    }
                    if ($row['user_type'] == 3) {
                        $name       = $row['d_name'];
                        $mobile     = $row['d_mobile'];
                    }
                    if ($row['user_type'] == 4) {
                        $name       = $row['r_name'];
                        $mobile     = $row['r_mobile'];
                    }

                    return  '<div class="align-self-center">
                                <b class="mb-0 prd-name text-danger">' . $name . '</b><br />
                                <b class="mb-0 prd-category text-secondary fs--1">' . $mobile . '</b>
                            </div>';
                })
                ->editColumn('amount', function ($row) {
                    return  '<b class="text-primary">₹ ' . $row['amount'] . '</b>';
                })
                ->addColumn('action', function ($row) {
                    $html = '';
                    if ($row['status'] == 0) {
                        $html = '<div class="">
                            <button class="updateStatus btn btn-outline-success btn-icon btn-sm" data-type="1" data-id="' . $row['id'] . '">
                                <i class="fa fa-check"></i>
                            </button>
                            <button class="updateStatus btn btn-outline-danger btn-icon btn-sm" data-type="2" data-id="' . $row['id'] . '">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>';
                    } else {
                        if ($row['status'] == 0)  $html = '<span class="badge badge-light-secondary">Pending</span>';
                        if ($row['status'] == 1)  $html = '<span class="badge badge-light-success">Approved</span>';
                        if ($row['status'] == 2)  $html = '<span class="badge badge-light-danger">Rejected</span>';
                    }
                    return $html;
                })
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->where('main_distributors.name', 'like', "%" . $keyword . "%");
                    $query->orWhere('distributors.name', 'like', "%" . $keyword . "%");
                    $query->orWhere('retailers.name', 'like', '%' . $keyword . '%');
                    $query->orWhere('main_distributors.mobile', 'like', "%" . $keyword . "%");
                    $query->orWhere('distributors.mobile', 'like', "%" . $keyword . "%");
                    $query->orWhere('retailers.mobile', 'like', '%' . $keyword . '%');
                    $query->orWhere('main_distributors.userId', 'like', "%" . $keyword . "%");
                    $query->orWhere('distributors.userId', 'like', "%" . $keyword . "%");
                    $query->orWhere('retailers.userId', 'like', '%' . $keyword . '%');
                })
                ->rawColumns(['request_number', 'action', 'amount', 'user_name'])
                ->make(true);
        }

        return view('payment_request.index');
    }

    public function payment_request_export(Request $request)
    {
        $query = PaymentRequest::query();
        $query->select(
            'payment_requests.id',
            'user_type',
            'request_number',
            'amount',
            'title',
            'reason',
            'description',
            'attachment',
            'payment_requests.status',
            'payment_requests.updated_at',
            'main_distributors.name as m_name',
            'distributors.name as d_name',
            'retailers.name as r_name',
            'main_distributors.mobile as m_mobile',
            'distributors.mobile as d_mobile',
            'retailers.mobile as r_mobile',
            'main_distributors.userId as m_userId',
            'distributors.userId as d_userId',
            'retailers.userId as r_userId',
        );

        $query->leftJoin('main_distributors', function ($join) {
            $join->on('main_distributors.id', '=', 'payment_requests.user_id');
            $join->where('user_type', 2);
        });
        $query->leftJoin('distributors', function ($join) {
            $join->on('distributors.id', '=', 'payment_requests.user_id');
            $join->where('user_type', 3);
        });
        $query->leftJoin('retailers', function ($join) {
            $join->on('retailers.id', '=', 'payment_requests.user_id');
            $join->where('user_type', 4);
        });

        if (request('start_date') && request('end_date')) {
            if (request('start_date') == request('end_date')) {
                $query->whereDate('payment_requests.updated_at', request('start_date'));
            } else {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->diffInDays($endDate) > 30) {
                    return back()->withInput()->with('error', "Report can be exported for max 30 Days.");
                }
                $query->whereBetween('payment_requests.updated_at', [$startDate, $endDate]);
            }
        } else {
            $startDate  = Carbon::now()->startOfDay()->subDays(7);
            $endDate    = Carbon::now();
            // dd($startDate->toDateTimeString(), $endDate->toDateTimeString());
            $query->whereBetween('payment_requests.updated_at', [$startDate, $endDate]);
        }

        $query->orderBy('payment_requests.id', 'desc');
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1st Sheet :: PanCard Summery
        $sheet->setTitle('Payment Request Report', true);

        $sheet->setCellValue('A1', 'Request ID');
        $sheet->setCellValue('B1', 'User Name');
        $sheet->setCellValue('C1', 'User Mobile');
        $sheet->setCellValue('D1', 'User User ID');
        $sheet->setCellValue('E1', 'User Type');
        $sheet->setCellValue('F1', 'Date');
        $sheet->setCellValue('G1', 'Amount');
        $sheet->setCellValue('H1', 'Status');

        $rows = 2;
        foreach ($data as $key => $value) {
            switch ($value->user_type) {
                case 2:
                    $name   =  $value->m_name;
                    $mobile =  $value->m_mobile;
                    $userId =  $value->m_userId;
                    $role   = 'Main Distributor';
                    break;
                case 3:
                    $name   =  $value->d_name;
                    $mobile =  $value->d_mobile;
                    $userId =  $value->d_userId;
                    $role   = 'Distributor';
                    break;
                case 4:
                    $name   =  $value->r_name;
                    $mobile =  $value->r_mobile;
                    $userId =  $value->r_userId;
                    $role   = 'Retailer';
                    break;
                default:
                    $name = 'n/a';
                    $role = '';
                    break;
            }

            switch ($value->status) {
                case 0:
                    $status = "Pending";
                    break;
                case 1:
                    $status = "Approved";
                    break;
                case 2:
                    $status = "Rejected";
                    break;
                default:
                    $status = "--";
                    break;
            }

            $sheet->setCellValue('A' . $rows, $value->request_number);
            $sheet->setCellValue('B' . $rows, $name);
            $sheet->setCellValue('C' . $rows, $mobile);
            $sheet->setCellValue('D' . $rows, $userId);
            $sheet->setCellValue('E' . $rows, $role);
            $sheet->setCellValue('F' . $rows, Date::PHPToExcel($value->updated_at));
            $sheet->setCellValue('G' . $rows, $value->amount);
            $sheet->setCellValue('H' . $rows, $status);
            $rows++;
        }

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $sheet->getStyle('A1:H' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F1:F' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('G1:G' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "PaymentRequest-Report.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function payment_request_update(Request $request)
    {
        $payment = PaymentRequest::find($request->id);
        if ($payment == null) {
            return response()->json([
                'status'    => false,
                'message'   => "Payment Request Not Found.",
                "data"      => ""
            ]);
        }

        if ($payment->user_type == 2) {
            $user   = MainDistributor::find($payment->user_id);
            if ($user == null) return $this->userNotFound();
            if ($request->type == 2) {
                return $this->rejectRequest($payment, $request->reason);
            } else {
                DB::transaction(function () use ($payment) {
                    LedgerController::add($payment->user_id, 2, [
                        'amount'            => $payment['amount'],
                        'payment_type'      => 1,
                        'payment_method'    => 1,
                        'particulars'       => "Admin Credited :: " . $payment->request_number,
                        'request_id'        => $payment['id']
                    ]);
                    $payment->update(['status'    => 1, 'reason'    => 'Request Approved.']);
                });

                return $this->approveRequest();
            }
        } elseif ($payment->user_type == 3) {
            $user   = Distributor::find($payment->user_id);
            if ($user == null) return $this->userNotFound();
            if ($request->type == 2) {
                return $this->rejectRequest($payment, $request->reason);
            } else {
                DB::transaction(function () use ($payment) {
                    LedgerController::add($payment->user_id, 3, [
                        'amount'            => $payment['amount'],
                        'payment_type'      => 1,
                        'payment_method'    => 1,
                        'particulars'       => "Admin Credited :: " . $payment->request_number,
                        'request_id'        => $payment['id']
                    ]);
                    $payment->update(['status'    => 1, 'reason'    => 'Request Approved.']);
                });

                return $this->approveRequest();
            }
        } elseif ($payment->user_type == 4) {
            $user   = Retailer::find($payment->user_id);
            if ($user == null) return $this->userNotFound();
            if ($request->type == 2) {
                return $this->rejectRequest($payment, $request->reason);
            } else {
                DB::transaction(function () use ($payment) {
                    LedgerController::add($payment->user_id, 4, [
                        'amount'            => $payment['amount'],
                        'payment_type'      => 1,
                        'payment_method'    => 1,
                        'particulars'       => "Admin Credited :: " . $payment->request_number,
                        'request_id'        => $payment['id']
                    ]);
                    $payment->update(['status'    => 1, 'reason'    => 'Request Approved.']);
                });

                return $this->approveRequest();
            }
        } else {
            return $this->userNotFound();
        }
    }

    // Procted Method used in Current Controller
    protected function rejectRequest(PaymentRequest $payment, $reason = '')
    {
        $payment->update(['status'    => 2, 'reason'    => $reason]);
        return response()->json([
            'status'    => true,
            'message'   => "Request Updated successfully.",
            "data"      => ""
        ]);
    }

    protected function approveRequest()
    {
        return response()->json([
            'status'    => true,
            'message'   => "Request Updated successfully.",
            "data"      => ""
        ]);
    }

    protected function userNotFound()
    {
        return response()->json([
            'status'    => false,
            'message'   => "User Not Found.",
            "data"      => ""
        ]);
    }
}
