<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use App\Library\Firebase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notification.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title'     => ['required', 'string', 'max:100'],
            'message'   => ['required', 'string', 'max:200'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'send_to'   => ['nullable', 'array', Rule::requiredIf($request->sendType == 'on')]
        ]);

        $query = Retailer::query();
        $query->whereNotNull('fcm_id');
        if ($request->sendType == 'on') {
            $query->whereIn('id', $request->send_to);
        }

        $firebaseToken  = $query->get();
        if ($firebaseToken->count()) {
            $image  = "";
            $path   = 'notification';
            if ($file = $request->file('image')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $image = asset('storage/' . $path . '/' . $uploadImage);
            }
            Firebase::sendMessage($firebaseToken->pluck('fcm_id')->toArray(), $request->title, $request->message, $image);
        }

        return redirect()->route('notification')->with('success', 'Message Sent Successfully..!!');
    }
}
