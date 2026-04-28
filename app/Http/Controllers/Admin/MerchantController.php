<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantController extends Controller
{
    public function index(): View
    {
        $merchants = Merchant::latest()->paginate(15);
        return view('admin.merchants.index', compact('merchants'));
    }

    public function create(): View
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'login' => 'required|string|max:100|unique:merchants,login|alpha_dash',
            'logo'  => 'nullable|image|max:2048',
        ]);

        $validated['auth_key'] = bin2hex(random_bytes(32));

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('merchant-logos', 'public');
        }

        Merchant::create($validated);

        return redirect()->route('admin.merchants.index')
            ->with('success', 'Merchant əlavə edildi.');
    }

    public function edit(Merchant $merchant): View
    {
        return view('admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'login' => 'required|string|max:100|alpha_dash|unique:merchants,login,' . $merchant->id,
            'logo'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('merchant-logos', 'public');
        }

        $merchant->update($validated);

        return redirect()->route('admin.merchants.index')
            ->with('success', 'Merchant yeniləndi.');
    }

    public function destroy(Merchant $merchant): RedirectResponse
    {
        $merchant->delete();
        return redirect()->route('admin.merchants.index')
            ->with('success', 'Merchant silindi.');
    }

    public function generateAuthKey(Merchant $merchant): JsonResponse
    {
        $key = bin2hex(random_bytes(32));
        $merchant->update(['auth_key' => $key]);
        return response()->json(['auth_key' => $key]);
    }
}
