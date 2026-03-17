<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::whereNotNull('sms_sent_at')->latest('sms_sent_at');

        if ($request->filled('phone')) {
            $query->byPhone($request->phone);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sms_sent_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sms_sent_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.sms-logs.index', compact('logs'));
    }
}
