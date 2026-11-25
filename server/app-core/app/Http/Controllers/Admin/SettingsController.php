<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', [
            'workStart' => config('midwife.work_start'),
            'lateUntil' => config('midwife.late_until'),
            'maxAccuracy' => config('midwife.max_gps_accuracy'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'password' => ['nullable', 'string', 'min:6'],
            'password_confirmation' => ['nullable', 'same:password'],
        ], [
            'name.required' => 'نام را وارد کنید.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل نامعتبر است.',
            'password.min' => 'رمز جدید حداقل 6 حرف باشد.',
            'password_confirmation.same' => 'تکرار رمز مطابقت ندارد.',
        ]);

        $user = $request->user();
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('ok', 'تنظیمات حساب ذخیره شد.');
    }
}
