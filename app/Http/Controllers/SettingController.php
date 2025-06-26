<?php

namespace App\Http\Controllers;

use App\Library\Database;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting as SettingModels;

class SettingController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->middleware('auth');
    }

    public function setting(Request $request, $id = null)
    {
        if ($request->method() == 'GET' && userCan(101, 'can_view')) {
            $setting = SettingModels::where('setting_type', $id)->where('status', 1)->get();
            if (count($setting) > 0) {
                $data = array(
                    'title'         => "Application Setting",
                    'type'          => empty(config('constant.setting_array')[$id]) ? "" : config('constant.setting_array')[$id],
                    'setting_id'    => $id,
                    'setting'       => $setting
                );


                return view('setting.index', $data);
            } else {
                return redirect()->route('dashboard')->with('error', 'No Settings Found..!!', 'Error');
            }
        }

        if ($request->method() == 'POST' && userCan(101, 'can_edit')) {
            if ($id == 1) {
                $request->validate([
                    'application_name'  => 'required|max:100',
                    'copyright'         => 'required|max:100',
                    'address'           => 'required|max:100',
                    'email'             => 'required|max:100',
                    'phone'             => 'required|max:100',
                    'favicon'           => 'image|mimes:jpg,png,jpeg,gif,svg|max:240',
                    'logo'              => 'image|mimes:jpg,png,jpeg,gif,svg|max:1024',
                ]);
            }

            if ($id == 2) {
                $request->validate([
                    'facebook'      => 'required|max:100',
                    'twitter'       => 'required|max:100',
                    'linkdin'       => 'required|max:100',
                    'instagram'     => 'required|max:100',
                ]);
            }

            if ($id == 3) {
                $request->validate([
                    'email_from'    => 'required|max:100',
                    'smtp_host'     => 'required|max:100',
                    'smtp_port'     => 'required|max:100',
                    'smtp_user'     => 'required|max:100',
                    'smtp_pass'     => 'required|max:100',
                ]);
            }

            if ($id == 4) {
                $request->validate([
                    'razorpay_key'    => 'required|max:100',
                    'razorpay_secret' => 'required|max:100',
                    'merchant_id'     => 'required|max:100',
                ]);
            }

            $inputs = $request->input();

            $path = 'application';
            if ($file = $request->file('favicon')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = 'favicon_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $inputs['favicon']        = $path . '/' . $uploadImage;

                $file = new Filesystem();
                $file->delete(base_path('storage\app\public\\' . $inputs['old_favicon']));
            }

            if ($file = $request->file('logo')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = 'logo_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $inputs['logo']        = $path . '/' . $uploadImage;

                $file = new Filesystem();
                $file->delete(base_path('storage\app\public\\' . $inputs['old_logo']));
            }

            if ($file = $request->file('load_money_qr_code')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = 'load_money_qr_code_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $inputs['load_money_qr_code']        = $path . '/' . $uploadImage;

                $file = new Filesystem();
                $file->delete(base_path('storage\app\public\\' . $inputs['old_load_money_qr_code']));
            }

            if ($file = $request->file('information_banner')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = 'information_banner_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $inputs['information_banner']        = $path . '/' . $uploadImage;

                $file = new Filesystem();
                $file->delete(base_path('storage\app\public\\' . $inputs['old_information_banner']));
            }

            if ($id == 4) {
                $inputs['is_commision'] = (!empty($inputs['is_commision']) && $inputs['is_commision'] == 'on') ? 1 : 0;
            }

            if ($id == 6) {
                $inputs['notify_modal_show'] = (!empty($inputs['notify_modal_show']) && $inputs['notify_modal_show'] == 'on') ? 1 : 0;
            }

            if ($id == 7) {
                $inputs['force_update_android'] = (!empty($inputs['force_update_android']) && $inputs['force_update_android'] == 'on') ? 1 : 0;
                $inputs['force_update_ios'] = (!empty($inputs['force_update_ios']) && $inputs['force_update_ios'] == 'on') ? 1 : 0;
                $inputs['maintenance_toggle'] = (!empty($inputs['maintenance_toggle']) && $inputs['maintenance_toggle'] == 'on') ? 1 : 0;
                $inputs['information_banner_toggle'] = (!empty($inputs['information_banner_toggle']) && $inputs['information_banner_toggle'] == 'on') ? 1 : 0;
            }

            foreach ($inputs as $key => $input) {
                SettingModels::where('setting_name', $key)->update(['filed_value' => $input]);
            }

            return redirect()->route('setting', ['id' => $id])->with('success', 'Setting Updated Successfully..!!');
        }

        return redirect()->route('dashboard')->with('error', 'Not Allowed..!!');
    }

    public function database_backup()
    {
        $path = Database::backup();
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
