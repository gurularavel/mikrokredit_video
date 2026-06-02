<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'og_title'          => 'nullable|string|max:255',
            'og_description'    => 'nullable|string|max:500',
            'og_image'          => 'nullable|image|max:2048',
            'og_image_url'      => 'nullable|url|max:500',
            'record_bottom_note'=> 'nullable|string|max:2000',
        ]);

        $data = [
            'og_title'           => $request->input('og_title'),
            'og_description'     => $request->input('og_description'),
            'record_bottom_note' => $request->input('record_bottom_note'),
        ];

        if ($request->hasFile('og_image')) {
            $old = SettingService::get('og_image_path');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('og_image')->store('og', 'public');
            $data['og_image_path'] = $path;
            $data['og_image_url']  = url(Storage::disk('public')->url($path));
        } elseif ($request->filled('og_image_url')) {
            $data['og_image_url'] = $request->input('og_image_url');
        }

        SettingService::setMany($data);

        return back()->with('success', 'Tənzimləmələr yadda saxlandı.');
    }
}
