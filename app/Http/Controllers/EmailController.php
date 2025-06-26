<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Retailer;
use App\Models\Distributor;
use Illuminate\Http\Request;
use App\Models\MainDistributor;
use Illuminate\Validation\Rule;
use App\Jobs\SendEmailNotification;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class EmailController extends Controller
{
    public function index()
    {
        return view('send_email.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title'     => ['required', 'string', 'max:100'],
            'message'   => ['required', 'string', 'max:2000'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'excel'     => ['mimes:xlsx,xls', 'max:2048',  Rule::requiredIf($request->sendType == 'on')]
        ]);

        $emails = [];

        if ($request->sendType == 'on' && $file = $request->file('excel')) {

            # Create a new Xls Reader
            $reader = new Xlsx();

            // Tell the reader to only read the data. Ignore formatting etc.
            $reader->setReadDataOnly(true);

            // Read the spreadsheet file.
            $spreadsheet = $reader->load($file->getPathName());

            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
            $data = $sheet->toArray();
            $emails = collect($data)->map(fn ($row) => ['name' => $row[0], 'email' => $row[1]])->toArray();
        }

        if (count($emails) == 0) {

            $a = $b = $c = $d = [];
            if ($request->all_main_distributor == 'on') {
                $a = MainDistributor::select('email', 'name')->where('status', 1)->get()->toArray();
            }

            if ($request->all_distributor == 'on') {
                $b = Distributor::select('email', 'name')->where('status', 1)->get()->toArray();
            }

            if ($request->all_retailer == 'on') {
                $c = Retailer::select('email', 'name')->where('status', 1)->get()->toArray();
            }

            if ($request->all_admins == 'on') {
                $d = User::select('email', 'name')->where('status', 1)->get()->toArray();
            }

            $emails = array_merge($a, $b, $c, $d);
        }

        $data = [
            'title'         => $request->title,
            'message'       => $request->message,
            'file'          => null
        ];

        $path = 'admin';
        if ($file = $request->file('file')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['file']        = asset('storage/' . $path . '/' . $uploadImage);
        }

        if (count($emails) > 0) {
            foreach ($emails as $value) {
                if (isValidEmail($value['email'])) {
                    SendEmailNotification::dispatch(array_merge($value, $data), $request->site_settings);
                }
            }
            return redirect()->route('emails')->with('success', 'Email Sent Successfully..!!');
        } else {
            return redirect()->route('emails')->with('error', 'No Emails were Sent..!!');
        }
    }
}
