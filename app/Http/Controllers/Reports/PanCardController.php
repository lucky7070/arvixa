<?php

namespace App\Http\Controllers\Reports;

use App\Models\PanCard;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PanCardController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // PanCards From Portal
            $query = PanCard::query();
            $query->selectRaw('user_id, retailers.name as username, retailers.mobile as user_mobile,  service_pancards.type, service_pancards.is_refunded, service_pancards.is_physical_card, service_pancards.nsdl_ack_no, service_pancards.nsdl_txn_id, service_pancards.nsdl_complete, service_pancards.created_at_gmt');
            $query->where('service_pancards.user_type', 4);
            $query->leftJoin('retailers', 'retailers.id', '=', 'service_pancards.user_id');

            if (request('is_physical_card')) {
                $query->where('is_physical_card', request('is_physical_card'));
            }

            if (request('is_refunded') || request('is_refunded')  === '0') {
                $query->where('is_refunded', request('is_refunded'));
            }

            if (request('type')) {
                $query->where('type', request('type'));
            }

            if (request('start_date') && request('end_date')) {
                if (request('start_date') == request('end_date')) {
                    $query->whereDate('service_pancards.created_at_gmt', request('start_date'));
                } else {
                    $startDate = Carbon::parse(request('start_date'));
                    $endDate = Carbon::parse(request('end_date'))->endOfDay();
                    $query->whereBetween('service_pancards.created_at_gmt', [$startDate, $endDate]);
                }
            }

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('nsdl_complete', function ($row) {
                    $status = '';
                    if ($row['nsdl_complete'] == 1 && $row['nsdl_ack_no'] != null && $row['is_refunded'] == 0) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Submitted</small>';
                    } elseif ($row['nsdl_complete'] == 1 && $row['nsdl_ack_no'] == null && $row['is_refunded'] == 1) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-dark"> Not Submitted</small>';
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Refunded</small>';
                    } elseif ($row['nsdl_complete'] == 1 && $row['nsdl_ack_no'] != null && $row['is_refunded'] == 1) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Submitted</small>';
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Refunded</small>';
                    } else {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> InComplete</small>';
                    }
                    return $status;
                })
                ->editColumn('username', function ($row) {
                    return  '<b>' . $row->username  . '</b><br><span>' .  $row->user_mobile . '</span>';
                })
                ->editColumn('nsdl_txn_id', function ($row) {
                    return '<b>' . $row->nsdl_txn_id . '</b>';
                })
                ->editColumn('is_physical_card', function ($row) {
                    return  $row->is_physical_card == 'Y' ? '<i class="fa-duotone fa-thumbs-up fa-2x text-success"></i>' : '<i class="fa-2x fa-duotone fa-thumbs-down text-danger"></i>';
                })
                ->editColumn('created_at_gmt', function ($row) {
                    return $row['created_at_gmt'] ? $row['created_at_gmt']->format('d M, Y') : '';
                })
                ->editColumn('type', function ($row) {
                    return $row['type'] == 1 ? '<span class="badge badge-light-primary">New</span>' : '<span class="badge badge-light-secondary">Update</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-outline-dark checkStatus p-1" data-ack-no="' . $row['nsdl_ack_no'] . '" data-txn-id="' . $row['nsdl_txn_id'] . '">Check Status</button>';
                    return $btn;
                })
                ->rawColumns(['action', 'nsdl_complete', 'is_physical_card', 'username', 'nsdl_txn_id', 'type'])
                ->make(true);
        }
        return view('reports.pan-card.index');
    }

    public function export()
    {
        if (request('start_date') && request('end_date')) {
            $startDate = Carbon::parse(request('start_date'));
            $endDate = Carbon::parse(request('end_date'))->endOfDay();
            if ($startDate->diffInDays($endDate) > 90) {
                return redirect()->back()->withInput()->with('error', "Report can be exported for max 90 Days.");
            }
        } else {
            $startDate  = Carbon::now()->startOfDay()->subDays(7);
            $endDate    = Carbon::now();
        }

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (request('id') == 1) {

            // Get Summery Data
            $qrSum = ServiceUsesLog::query();
            $qrSum->selectRaw('
                count(id) as count, 
                sum(purchase_rate) as purchase_rate ,
                sum(sale_rate) as sale_rate ,
                sum(main_distributor_commission) as main_distributor_commission ,
                sum(distributor_commission) as distributor_commission ,
                sum(is_refunded) as is_refunded ,
                Date(created_at_gmt) as date');

            $qrSum->whereIn('service_id', [
                config('constant.service_ids.pan_cards_add'),
                config('constant.service_ids.pan_cards_add_digital'),
                config('constant.service_ids.pan_cards_edit'),
                config('constant.service_ids.pan_cards_edit_digital'),
            ]);

            $qrSum->where('is_refunded', 0);
            if ($startDate->eq($endDate)) {
                $startDateStr = $startDate->format('Y-m-d');
                $qrSum->whereDate('created_at_gmt', $startDateStr);
            } else {
                $qrSum->whereBetween('created_at_gmt', [$startDate, $endDate]);
            }

            $qrSum->groupBy(DB::raw('Date(created_at_gmt)'));
            $getSummery = $qrSum->get();

            // 1st Sheet :: PanCard Summery
            $sheet->setTitle('PanCard Summery', true);
            $sheet->setCellValue('A1', 'PanCard Report');

            $sheet->mergeCells('A1:F1', Worksheet::MERGE_CELL_CONTENT_MERGE);
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);
            $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');
            $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:F1')->getFont()->getColor()->setARGB('000000');
            $sheet->getStyle('A1:F1')->getBorders()->getBottom()->setBorderStyle('thick');
            $sheet->getStyle('A1:F1')->getFont()->setSize(16);

            $sheet->setCellValue('A2', 'Created Date');
            $sheet->setCellValue('B2', 'Total Purchase Rate');
            $sheet->setCellValue('C2', 'Total Sale Rate');
            $sheet->setCellValue('D2', 'Total Main Distributor Commission');
            $sheet->setCellValue('E2', 'Total Distributor Commission');
            $sheet->setCellValue('F2', 'Total PanCard Created');

            $rows = 3;
            foreach ($getSummery as $key => $value) {
                $sheet->setCellValue('A' . $rows, Date::PHPToExcel($value->date));
                $sheet->setCellValue('B' . $rows, $value->purchase_rate);
                $sheet->setCellValue('C' . $rows, $value->sale_rate);
                $sheet->setCellValue('D' . $rows, $value->main_distributor_commission);
                $sheet->setCellValue('E' . $rows, $value->distributor_commission);
                $sheet->setCellValue('F' . $rows, $value->count);
                $rows++;
            }

            $sheet->setCellValue('A' . $rows, 'Total :');
            $sheet->setCellValue('B' . $rows, $getSummery->sum('purchase_rate'));
            $sheet->setCellValue('C' . $rows, $getSummery->sum('sale_rate'));
            $sheet->setCellValue('D' . $rows, $getSummery->sum('main_distributor_commission'));
            $sheet->setCellValue('E' . $rows, $getSummery->sum('distributor_commission'));
            $sheet->setCellValue('F' . $rows, $getSummery->sum('count'));

            $sheet->getStyle('A1:F' . $rows)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $rows . ':F' . $rows)->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getStyle('A' . $rows . ':F' . $rows)->getBorders()->getTop()->setBorderStyle('thin');
            $sheet->getStyle('A' . $rows . ':F' . $rows)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rows . ':F' . $rows)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

            $sheet->getStyle('A3:A' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_XLSX15);
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle('A2:' . $highestColumn . '2')->getFont()->setBold(true);
            $sheet->getStyle('A2:F2')->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getStyle('B3:E' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
            foreach ($sheet->getColumnIterator() as $column) {
                if ($column->getColumnIndex() != 'A')
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                else
                    $sheet->getColumnDimension($column->getColumnIndex())->setWidth(15);
            }

            $sheet->setAutoFilter('A2:F' . $rows);

            $notes = [
                "This  report only included successfully created and Under Process Pancard data.",
                "Refunded Pancards data not in include in this report.",
                'More Details you can check sheet "PanCard List".',
            ];

            $rows = $rows + 2;
            $line = 'A' . $rows++;
            $sheet->setCellValue($line, "Notes : ");
            $sheet->getStyle($line)->getFont()->setBold(true);
            foreach ($notes as $key => $value) {
                $sheet->setCellValue('A' . $rows, $key + 1 . ' : ' . $value);
                ++$rows;
            }
        }

        //=======================================================================================================
        if (request('id') == 2) {
            // PanCards From Portal
            $query = PanCard::query();
            $query->selectRaw('user_id, retailers.name as username, retailers.mobile as user_mobile, 0 as company_id,  service_pancards.type, service_pancards.is_refunded, service_pancards.is_physical_card, service_pancards.nsdl_ack_no, service_pancards.nsdl_txn_id, service_pancards.nsdl_complete, service_pancards.created_at_gmt, error_message');
            $query->where('service_pancards.user_type', 4);
            $query->leftJoin('retailers', 'retailers.id', '=', 'service_pancards.user_id');

            if (request('is_physical_card')) {
                $query->where('is_physical_card', request('is_physical_card'));
            }
            if (request('type')) {
                $query->where('type', request('type'));
            }

            if ($startDate->eq($endDate)) {
                $startDateStr = $startDate->format('Y-m-d');
                $query->whereDate('service_pancards.created_at_gmt', $startDateStr);
            } else {
                $query->whereBetween('service_pancards.created_at_gmt', [$startDate, $endDate]);
            }

            if (request('is_refunded') || request('is_refunded')  === '0') {
                $query->where('is_refunded', request('is_refunded'));
            }

            $query->orderBy('created_at_gmt', 'desc');

            $sheet->setTitle('PanCard List', true);

            $sheet->setCellValue('A1', 'Created By');
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'User Name');
            $sheet->setCellValue('D1', 'User Mobile');
            $sheet->setCellValue('E1', 'PanCard TXN Id');
            $sheet->setCellValue('F1', 'PanCard Acknowledgement');
            $sheet->setCellValue('G1', 'Use Type');
            $sheet->setCellValue('H1', 'Physical Card');
            $sheet->setCellValue('I1', 'Completed');
            $sheet->setCellValue('J1', 'Is Refunded');
            $sheet->setCellValue('K1', 'Message');

            $rows = 2;
            foreach ($query->get() as $row) {
                $sheet->setCellValue('A' . $rows, 'Retailer');
                $sheet->setCellValue('B' . $rows, Date::PHPToExcel($row->created_at_gmt));
                $sheet->setCellValue('C' . $rows, $row->username);
                $sheet->setCellValue('D' . $rows, $row->user_mobile);
                $sheet->setCellValue('E' . $rows, $row->nsdl_txn_id);
                $sheet->setCellValue('F' . $rows, $row->nsdl_ack_no);
                $sheet->setCellValue('G' . $rows, $row->type == 1 ? 'New' : 'Correction');
                $sheet->setCellValue('H' . $rows, $row->is_physical_card == 'Y' ? 'Yes' : 'No');
                $sheet->setCellValue('I' . $rows, $row->nsdl_complete == 1 ? 'Yes' : 'No');
                $sheet->setCellValue('J' . $rows, $row->is_refunded == 1 ? 'Yes' : 'No');

                // Change Error Message Format...
                $parsed = json_decode($row->error_message, true);
                if ($parsed != null && gettype($parsed) == 'array') {
                    $error = implode(',', array_values($parsed));
                } else {
                    $error = $row->error_message == 'null' ? "" : $row->error_message;
                }

                $sheet->setCellValue('K' . $rows, $error);
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

            $sheet->getStyle('A1:J' . $rows)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B1:B' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $sheet->getStyle('D1:F' . $rows)->getNumberFormat()->setFormatCode('#');
            $sheet->getStyle('A1');
        }

        //=======================================================================================================
        if (request('id') == 3) {

            DB::statement("SET SQL_MODE = ''");
            $cardsCountByRetailers = PanCard::select('retailers.id', 'retailers.name', 'retailers.email', 'retailers.mobile', 'retailers.user_balance', 'employees.name as emp_name')
                ->selectRaw('count(*) as pancards_count')
                ->join('retailers', 'service_pancards.user_id', '=', 'retailers.id')
                ->leftJoin('employees', 'employees.id', '=', 'retailers.employee_id')
                ->where('service_pancards.user_type', 4)
                ->where('service_pancards.nsdl_complete', 1)
                ->whereNotNull('service_pancards.nsdl_ack_no')
                ->groupBy('service_pancards.user_id')
                ->get();

            $sheet->setTitle('Retailer PanCard Count', true);

            $sheet->setCellValue('A1', 'Retailer Name');
            $sheet->setCellValue('B1', 'Retailer Email');
            $sheet->setCellValue('C1', 'Retailer Mobile');
            $sheet->setCellValue('D1', 'Retailer Balance');
            $sheet->setCellValue('E1', 'Employee Name');
            $sheet->setCellValue('F1', 'Total PanCard Count');

            $rows = 2;
            foreach ($cardsCountByRetailers as $key => $row) {
                $sheet->setCellValue('A' . $rows, $row->name);
                $sheet->setCellValue('B' . $rows, $row->email);
                $sheet->setCellValue('C' . $rows, $row->mobile);
                $sheet->setCellValue('D' . $rows, $row->user_balance);
                $sheet->setCellValue('E' . $rows, $row->emp_name);
                $sheet->setCellValue('F' . $rows, $row->pancards_count);
                $rows++;
            }

            $sheet->setCellValue('C' . $rows, "Total : ");
            $sheet->setCellValue('D' . $rows, $cardsCountByRetailers->sum('user_balance'));

            // Header Row Bold
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

            // AutoWidth Column
            $sheet->getStyle('A1:G' . $rows)->getAlignment()->setHorizontal('center');
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $sheet->getStyle('D1:D' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        }


        //=======================================================================================================

        $fileName = "PanCardsExport.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function statistics()
    {
        if (request('start_date') && request('end_date')) {
            $startDate  = Carbon::parse(request('start_date'));
            $endDate    = Carbon::parse(request('end_date'))->endOfDay();
        } else {
            $startDate  = Carbon::parse('2023-01-01');
            $endDate    = Carbon::now();
        }

        $data = collect(DB::select("
            select 
                count(id) as total, 
                sum(nsdl_complete) as nsdl_complete, 
                sum(is_refunded) as is_refunded, 
                count(nsdl_ack_no) as nsdl_ack_no 
            from 
                `service_pancards` 
            where 
                `created_at_gmt` between ? and ?
            ", [$startDate, $endDate]))->first();

        return response()->json([
            'status'    => true,
            'message'   => "Success",
            'data'      => $data
        ]);
    }
}
