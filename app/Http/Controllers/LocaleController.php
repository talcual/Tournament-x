<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');
        $allowed = config('app.available_locales', ['en']);

        if (! in_array($locale, $allowed, true)) {
            $locale = config('app.locale');
        }

        $request->session()->put('locale', $locale);

        return redirect()->back()->with('status', __('app.switched'));
    }
}
