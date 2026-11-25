<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use App\Models\Midwife;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.notifications', [
            'notifications' => Notification::with(['cluster', 'midwife', 'creator'])->orderByDesc('created_at')->limit(100)->get(),
            'clusters' => Cluster::orderBy('code')->get(),
            'midwives' => Midwife::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
            'target' => ['required', 'in:all,cluster,midwife'],
            'cluster_id' => ['nullable', 'required_if:target,cluster', 'exists:clusters,id'],
            'midwife_id' => ['nullable', 'required_if:target,midwife', 'exists:midwives,id'],
        ], [
            'title.required' => 'عنوان اعلان را بنویسید.',
            'body.required' => 'متن اعلان را بنویسید.',
            'target.in' => 'گیرنده نامعتبر است.',
            'cluster_id.required_if' => 'کلستر را انتخاب کنید.',
            'midwife_id.required_if' => 'قابله را انتخاب کنید.',
        ]);

        Notification::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'target' => $data['target'],
            'cluster_id' => $data['target'] === 'cluster' ? $data['cluster_id'] : null,
            'midwife_id' => $data['target'] === 'midwife' ? $data['midwife_id'] : null,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('ok', 'اعلان ارسال شد.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Notification::findOrFail($id)->delete();

        return back()->with('ok', 'اعلان حذف شد.');
    }
}
