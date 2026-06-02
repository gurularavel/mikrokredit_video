<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendApplicationSms;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::latest();

        if ($request->filled('phone')) {
            $query->byPhone($request->phone);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function create()
    {
        return view('admin.applications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'regex:/^\+994[0-9]{9}$/'],
            'amount'  => ['nullable', 'numeric', 'min:0'],
        ], [
            'name.required'    => 'Ad daxil edin.',
            'surname.required' => 'Soyad daxil edin.',
            'phone.required'   => 'Telefon nömrəsi daxil edin.',
            'phone.regex'      => 'Telefon nömrəsi +994XXXXXXXXX formatında olmalıdır.',
            'amount.numeric'   => 'Məbləğ rəqəm olmalıdır.',
            'amount.min'       => 'Məbləğ mənfi ola bilməz.',
        ]);

        $expiryMinutes = (int) config('sms.expiry_minutes', 60);
        $token         = Str::random(64);
        $accessToken   = Str::random(64);

        $application = Application::create([
            'name'             => $validated['name'],
            'surname'          => $validated['surname'],
            'phone'            => $validated['phone'],
            'amount'           => $validated['amount'] ?? null,
            'token'            => $token,
            'access_token'     => $accessToken,
            'token_expires_at' => now()->addMinutes($expiryMinutes),
            'status'           => 'pending',
        ]);

        SendApplicationSms::dispatch($application);

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Müraciət yaradıldı və SMS göndərildi.');
    }

    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => ['required', 'in:pending,recorded,reviewed'],
        ]);

        $application->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status uğurla yeniləndi.');
    }

    public function updateNotes(Request $request, Application $application)
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $application->update(['admin_notes' => $request->admin_notes]);

        return redirect()->back()->with('success', 'Qeyd uğurla saxlanıldı.');
    }

    public function streamVideo(Application $application)
    {
        if (!$application->video_path) {
            abort(404);
        }

        $disk = $application->video_disk ?: 'local';
        $fullPath = Storage::disk($disk)->path($application->video_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mime = str_ends_with($application->video_path, '.mp4') ? 'video/mp4' : 'video/webm';

        return response()->file($fullPath, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
