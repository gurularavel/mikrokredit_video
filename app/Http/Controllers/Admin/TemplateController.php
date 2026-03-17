<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $sms  = MessageTemplate::where('group', 'sms')->orderBy('id')->get();
        $page = MessageTemplate::where('group', 'page')->orderBy('id')->get();

        return view('admin.templates.index', compact('sms', 'page'));
    }

    public function update(Request $request, MessageTemplate $template)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $template->update(['content' => $request->content]);
        TemplateService::forget($template->key);

        return back()->with('success', '"' . $template->label . '" uğurla yeniləndi.');
    }
}
