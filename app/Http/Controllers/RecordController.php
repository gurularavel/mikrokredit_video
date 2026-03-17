<?php

namespace App\Http\Controllers;

use App\Jobs\CompressVideo;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    public function show(string $token)
    {
        $application = Application::where('token', $token)->first();

        if (!$application) {
            return view('public.error', ['reason' => 'not_found']);
        }

        if ($application->isTokenExpired()) {
            return view('public.error', ['reason' => 'expired']);
        }

        if ($application->isTokenUsed()) {
            return view('public.error', ['reason' => 'used']);
        }

        return view('public.record', [
            'application' => $application,
            'duration'    => config('video.duration', 15),
        ]);
    }

    public function upload(Request $request, string $token)
    {
        $application = Application::where('token', $token)->first();

        if (!$application || $application->isTokenExpired() || $application->isTokenUsed()) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 422);
        }

        $maxKb = config('video.max_size_kb', 51200);

        $request->validate([
            'video'     => ['required', 'file', 'max:' . $maxKb],
            'mime_type' => ['required', 'string'],
        ]);

        $disk = config('video.disk', 'public');
        $dir  = 'videos/' . now()->format('Y/m');
        $ext  = str_contains($request->mime_type, 'mp4') ? 'mp4' : 'webm';
        $filename = $application->id . '_' . now()->format('His') . '.' . $ext;
        $path = $dir . '/' . $filename;

        Storage::disk($disk)->putFileAs($dir, $request->file('video'), $filename);

        $application->update([
            'video_path'        => $path,
            'video_disk'        => $disk,
            'video_size'        => $request->file('video')->getSize(),
            'video_recorded_at' => now(),
            'status'            => 'recorded',
        ]);

        CompressVideo::dispatch($application);

        return response()->json([
            'success'  => true,
            'redirect' => route('record.complete', $token),
        ]);
    }

    public function complete(string $token)
    {
        $application = Application::where('token', $token)->first();

        if (!$application) {
            return view('public.error', ['reason' => 'not_found']);
        }

        return view('public.complete', ['application' => $application]);
    }
}
