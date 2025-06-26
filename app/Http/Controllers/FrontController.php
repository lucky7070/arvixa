<?php

namespace App\Http\Controllers;

use App\Models\Cms;
use App\Models\Faq;
use App\Models\Slider;
use App\Models\Services;
use App\Models\Enquiries;
use App\Models\JoinRequest;
use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontController extends Controller
{

    public function home()
    {
        // Banners Data
        $all_banner = Slider::where('status', 1)->get();
        $main_banner    = $all_banner->filter(fn ($item)  => $item->is_special == 0)->values();
        $sec_banner     = $all_banner->filter(fn ($item)  => $item->is_special == 1)->values();

        // About Us CMS Data
        $about_us       = Cms::find(1);

        // Testimonial Data
        $testimonials   = Testimonial::where('status', 1)->limit(10)->get();

        // Services Data
        $services       = Services::where('status', 1)->where('is_feature', 1)->get();

        return view('front.home', compact('main_banner', 'sec_banner', 'about_us', 'testimonials', 'services'));
    }

    public function about()
    {
        $data = Cms::find(2);
        return view('front.about', compact('data'));
    }

    public function terms_and_condition()
    {
        $data = Cms::find(4);
        return view('front.terms_and_condition', compact('data'));
    }

    public function privacy_policy()
    {
        $data = Cms::find(3);
        return view('front.privacy_policy', compact('data'));
    }

    public function contact()
    {
        $faqs = Faq::where('status', 1)->get();
        return view('front.contact', compact('faqs'));
    }

    public function contact_save(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'phone'             => ['required', 'digits:10'],
            'email'             => ['required', 'string', 'email', 'max:255'],
            'message'           => ['required', 'string', 'max:500'],
        ]);

        $data = [
            'name'              => $request->name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'message'           => $request->message,
        ];

        Enquiries::create($data);
        return redirect(route('contact'))->with('success', 'Message Saved Successfully!!');
    }

    public function testimonial()
    {
        $testimonials   = Testimonial::where('status', 1)->paginate(9);
        return view('front.testimonial', compact('testimonials'));
    }

    public function services()
    {
        $services       = Services::where('status', 1)->get();
        return view('front.services', compact('services'));
    }

    public function join_us()
    {
        return view('front.join_us');
    }

    public function join_us_save(Request $request)
    {
        $request->validate([
            'request_for'       => ['required', 'integer', 'in:2,3,4'],
            'name'              => ['required', 'string', 'max:50'],
            'phone'             => ['required', 'digits:10'],
            'email'             => ['required', 'string', 'email', 'max:50'],
            'message'           => ['required', 'string', 'max:500'],
        ]);

        $data = [
            'request_for'       => $request->request_for,
            'name'              => $request->name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'message'           => $request->message,
        ];

        JoinRequest::create($data);
        return redirect(route('join_us'))->with('success', 'Request Sent Successfully!!');
    }
}
