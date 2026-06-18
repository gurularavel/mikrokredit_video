<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Jobs\SendApplicationSms;
use App\Models\Application;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        return view('public.index');

    }

    public function store(StoreApplicationRequest $request)
    {
        $expiryMinutes = (int) config('sms.expiry_minutes', 60);

        $application = Application::create([
            'name'             => $request->name,
            'surname'          => $request->surname,
            'phone'            => $request->phone,
            'token'            => Str::random(64),
            'token_expires_at' => now()->addMinutes($expiryMinutes),
            'status'           => 'pending',
        ]);

        SendApplicationSms::dispatch($application);

        return redirect()->route('applications.sent');
    }

    public function sent()
    {
        return view('public.sent');
    }
}
