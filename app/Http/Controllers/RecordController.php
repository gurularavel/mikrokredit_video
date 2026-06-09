<?php

namespace App\Http\Controllers;

use App\Jobs\CompressVideo;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $logCtx = ['token' => substr($token, 0, 12) . '...'];

        Log::channel('video_upload')->info('Upload başladı', array_merge($logCtx, [
            'ip'           => $request->ip(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'has_file'     => $request->hasFile('video'),
            'files'        => array_keys($request->allFiles()),
        ]));

        $application = Application::where('token', $token)->first();

        if (!$application) {
            Log::channel('video_upload')->warning('Token tapılmadı', $logCtx);
            return response()->json(['success' => false, 'message' => 'Invalid token'], 422);
        }

        if ($application->isTokenExpired()) {
            Log::channel('video_upload')->warning('Token vaxtı bitib', $logCtx);
            return response()->json(['success' => false, 'message' => 'Invalid token'], 422);
        }

        if ($application->isTokenUsed()) {
            Log::channel('video_upload')->warning('Token artıq istifadə edilib', $logCtx);
            return response()->json(['success' => false, 'message' => 'Invalid token'], 422);
        }

        $maxKb = config('video.max_size_kb', 51200);

        Log::channel('video_upload')->info('Validation başladı', array_merge($logCtx, [
            'max_kb'       => $maxKb,
            'file_size'    => $request->hasFile('video') ? $request->file('video')->getSize() : null,
            'file_error'   => $request->hasFile('video') ? $request->file('video')->getError() : null,
            'mime_type'    => $request->input('mime_type'),
        ]));

        try {
            $request->validate([
                'video'     => ['required', 'file', 'max:' . $maxKb],
                'mime_type' => ['required', 'string'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('video_upload')->error('Validation xətası', array_merge($logCtx, [
                'errors' => $e->errors(),
            ]));
            throw $e;
        }

        $disk = config('video.disk', 'public');
        $dir  = 'videos/' . now()->format('Y/m');
        $ext  = str_contains($request->mime_type, 'mp4') ? 'mp4' : 'webm';
        $filename = $application->id . '_' . now()->format('His') . '.' . $ext;
        $path = $dir . '/' . $filename;

        Log::channel('video_upload')->info('Saxlanılır', array_merge($logCtx, [
            'disk' => $disk,
            'path' => $path,
            'size' => $request->file('video')->getSize(),
        ]));

        try {
            Storage::disk($disk)->putFileAs($dir, $request->file('video'), $filename);
        } catch (\Throwable $e) {
            Log::channel('video_upload')->error('Storage xətası', array_merge($logCtx, [
                'error' => $e->getMessage(),
            ]));
            return response()->json(['success' => false, 'message' => 'Fayl saxlanılmadı: ' . $e->getMessage()], 500);
        }

        $application->update([
            'video_path'        => $path,
            'video_disk'        => $disk,
            'video_size'        => $request->file('video')->getSize(),
            'video_recorded_at' => now(),
            'status'            => 'recorded',
        ]);

        CompressVideo::dispatch($application);

        Log::channel('video_upload')->info('Upload uğurlu', array_merge($logCtx, [
            'path' => $path,
        ]));

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
