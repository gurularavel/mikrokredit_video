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

        if (!$application->link_opened_at) {
            $application->update(['link_opened_at' => now()]);
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

    public function showVideo(string $appId)
    {
        $application = Application::where('app_id', $appId)->latest()->first();

        if (!$application || !$application->video_path) {
            abort(404);
        }

        return view('public.video', ['application' => $application]);
    }

    public function streamVideo(string $appId)
    {
        $application = Application::where('app_id', $appId)->latest()->first();

        if (!$application || !$application->video_path) {
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
