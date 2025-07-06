<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Services::select('id', 'name', 'slug', 'banner', 'image', 'status', 'created_at');

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
                    if (userCan(107, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('services.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('services.commission_slots', $row['slug']) . '">Commission Slots</a>';
                    }
                    if (userCan(107, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(107)) {
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
        return view('services.index');
    }

    public function add()
    {
        return view('services.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'status'                => ['required', 'integer'],
            'purchase_rate'         => ['required', 'numeric'],
            'sale_rate'             => ['required', 'numeric'],
            'default_d_commission'  => ['required', 'numeric'],
            'default_md_commission' => ['required', 'numeric'],
            'default_r_commission'  => ['required', 'numeric'],
            'default_assign'        => ['required', 'integer'],
            'description'           => ['required', 'string'],
            'is_feature'            => ['required', 'integer'],
            'btn_text'              => ['required', 'string', 'max:255'],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'banner'                => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'                  => Str::uuid(),
            'name'                  => $request->name,
            'description'           => $request->description,
            'purchase_rate'         => $request->purchase_rate,
            'sale_rate'             => $request->sale_rate,
            'default_d_commission'  => $request->default_d_commission,
            'default_md_commission' => $request->default_md_commission,
            'default_r_commission'  => $request->default_r_commission,
            'default_assign'        => $request->default_assign,
            'status'                => $request->status,
            'is_feature'            => $request->is_feature,
            'btn_text'              => $request->btn_text,
            'commission_slots'      => config('constant.bill-slab', []),
            'banner'                => 'services/banner.jpg',
            'image'                 => 'services/image.png',
        ];

        $path = 'services';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        if ($file = $request->file('banner')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['banner']        = $path . '/' . $uploadImage;
        }

        if (floatval($request->sale_rate) > (floatval($request->purchase_rate) + floatval($request->default_d_commission) + floatval($request->default_md_commission))) {
            Services::create($data);
            return to_route('services')->with('success', 'Service Added Successfully!!');
        }

        return back()->withInput()->with('error', "Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.");
    }

    public function edit($id)
    {
        $service = Services::firstWhere('slug', $id);
        if ($service == null) {
            return to_route('services')->with('error', 'Service Not Found!!');
        }

        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Services::firstWhere('id', $id);
        if ($service == null) {
            return to_route('services')->with('error', 'Service Not Found!!');
        }

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'min:1', 'max:50'],
            'description'           => ['required', 'string', 'min:1', 'max:50000'],
            'status'                => ['required', 'integer'],
            'default_assign'        => ['required', 'integer'],
            'is_feature'            => ['required', 'integer'],
            'btn_text'              => ['required', 'string', 'min:5', 'max:20'],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'banner'                => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'name'                  => $validated['name'],
            'description'           => $validated['description'],
            'default_assign'        => $validated['default_assign'],
            'is_feature'            => $validated['is_feature'],
            'btn_text'              => $validated['btn_text'],
            'status'                => $validated['status'],
        ];

        $path = 'services';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        if ($file = $request->file('banner')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['banner']        = $path . '/' . $uploadImage;
        }

        $service->update($data);
        return to_route('services')->with('success', 'Service Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $service = Services::where('id', $request->id)->first();
            if ($service == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Service Not Found.',
                ]);
            }

            ServicesLog::where('service_id', $service->id)->where('status', 1)->update([
                'status'        => 0,
                'decline_date'  => Carbon::now()
            ]);

            $service->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Service deleted Successfully',
            ]);
        }
    }

    public function commission_slots(Request $request, $slug)
    {
        $service = Services::firstWhere('slug', $slug);
        if ($service == null) {
            return to_route('services')->with('error', 'Service Not Found!!');
        }

        return view('services.commission_slots', compact('service'));
    }

    public function commission_slots_save(Request $request, $slug)
    {
        $service = Services::firstWhere('slug', $slug);
        if ($service == null) {
            return to_route('services')->with('error', 'Service Not Found!!');
        }

        if (in_array($service->id, config('constant.commission-slab-services', []))) {

            $validator = Validator::make($request->all(), [
                'commission_slots.*.start'                          => ['required', 'numeric', 'min:1', 'max:10000000'],
                'commission_slots.*.end'                            => ['required', 'numeric', 'min:1', 'max:10000000'],
                'commission_slots.*.commission'                     => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.commission_distributor'         => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.commission_main_distributor'    => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.total_commission'               => ['required', 'numeric', 'min:0', 'max:100'],
            ]);

            // Add your custom after validation
            $validator->after(function ($validator) use ($request) {
                foreach ($request->commission_slots as $index => $slot) {
                    $sum = $slot['commission'] + $slot['commission_distributor'] + $slot['commission_main_distributor'];

                    if ($sum > 100) {
                        $validator->errors()->add(
                            "commission_slots.$index.total_commission",
                            "The sum of all commissions (currently {$sum}%) must not exceed 100%"
                        );
                    }

                    // Option 2: Validate against total_commission value
                    if (abs($sum - $slot['total_commission']) > 0.01) {
                        $validator->errors()->add(
                            "commission_slots.$index.total_commission",
                            "The sum of individual commissions (currently {$sum}%) must equal the total commission value ({$slot['total_commission']}%)"
                        );
                    }
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Get validated data (equivalent to $request->validate() result)
            $validated = $validator->validated();

            $isSame = true;
            foreach (config('constant.bill-slab', []) as $key => $value) {
                if (@$validated['commission_slots'][$key]['start']  != $value['start']  || @$validated['commission_slots'][$key]['end']  != $value['end']) {
                    $isSame = false;
                    break;
                }
            }

            if (!$isSame)  return back()->with('error', 'Invalid Inputs..!!');
            if ($validated['commission_slots'] == $service->commission_slots) {
                return to_route('services')->with('success', 'No changes..!!');
            }

            $service_logs = ServicesLog::where('service_id', $service->id)->where('status', 1);

            $newData = array();
            foreach ($service_logs->get() as $key => $service_log) {
                array_push($newData, [
                    'user_id'                       => $service_log->user_id,
                    'service_id'                    => $service_log->service_id,
                    'user_type'                     => $service_log->user_type,
                    'purchase_rate'                 => $service_log->purchase_rate,
                    'sale_rate'                     => $service_log->sale_rate,
                    'main_distributor_commission'   => $service_log->main_distributor_commission,
                    'distributor_commission'        => $service_log->distributor_commission,
                    'retailer_commission'           => $service_log->retailer_commission,
                    'main_distributor_id'           => $service_log->main_distributor_id,
                    'distributor_id'                => $service_log->distributor_id,

                    'status'                        => 1,
                    'commission_slots'              => json_encode($validated['commission_slots']),
                    'assign_date'                   => now(),
                    'decline_date'                  => null,
                    'created_at'                    => now(),
                    'updated_at'                    => now(),
                ]);
            }

            DB::beginTransaction();
            try {

                $service->update(['commission_slots' =>  $validated['commission_slots']]);
                $service_logs->update(['status'        => 0, 'decline_date'  => Carbon::now()]);
                if (count($newData) > 0)   ServicesLog::insert($newData);

                DB::commit();
                return to_route('services')->with('success', 'Service Updated Successfully!!');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Something went wrong..!!' . $e->getMessage());
            }
        } else {

            $validated = $request->validate([
                'purchase_rate'         => ['required', 'numeric', 'min:0', 'max:1000000'],
                'sale_rate'             => ['required', 'numeric', 'min:0', 'max:1000000'],
                'default_d_commission'  => ['required', 'numeric', 'min:0', 'max:1000000'],
                'default_md_commission' => ['required', 'numeric', 'min:0', 'max:1000000'],
                'default_r_commission'  => ['required', 'numeric', 'min:0', 'max:1000000'],
            ]);

            if (floatval($request->sale_rate) >= (floatval($request->purchase_rate) + floatval($request->default_d_commission) + floatval($request->default_md_commission) + floatval($request->default_r_commission))) {
                if (
                    $service['purchase_rate']          != $request->purchase_rate          ||
                    $service['sale_rate']              != $request->sale_rate              ||
                    $service['default_d_commission']   != $request->default_d_commission   ||
                    $service['status']                 != $request->status                 ||
                    $service['default_md_commission']  != $request->default_md_commission
                ) {
                    $service_logs = ServicesLog::where('service_id', $service->id)->where('status', 1);
                    $newData = array();
                    foreach ($service_logs->get() as $key => $service_log) {
                        array_push($newData, [
                            'user_id'                       => $service_log->user_id,
                            'service_id'                    => $service_log->service_id,
                            'user_type'                     => $service_log->user_type,
                            'status'                        => 1,
                            'assign_date'                   => Carbon::now(),
                            'decline_date'                  => null,
                            'purchase_rate'                 => $request->purchase_rate,
                            'sale_rate'                     => $request->sale_rate,
                            'main_distributor_commission'   => $request->default_md_commission,
                            'distributor_commission'        => $request->default_d_commission,
                            'retailer_commission'           => $request->default_r_commission,
                            'commission_slots'              => $service_log->commission_slots,
                            'main_distributor_id'           => $service_log->main_distributor_id,
                            'distributor_id'                => $service_log->distributor_id,
                            'created_at'                    => Carbon::now(),
                            'updated_at'                    => Carbon::now(),
                        ]);
                    }

                    $service_logs->update(['status'        => 0, 'decline_date'  => Carbon::now()]);
                    if (count($newData) > 0 && $request->status == 1) {
                        ServicesLog::insert($newData);
                    }
                }

                $service->update($validated);
                return to_route('services')->with('success', 'Service Updated Successfully!!');
            } else {
                return back()->withInput()->with('error', "Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.");
            }
        }
    }
}
