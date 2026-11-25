<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClusterAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.clusters.index', [
            'clusters' => Cluster::withCount('midwives')->orderBy('code')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.clusters.form', ['cluster' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['code'] = $this->nextCode();
        Cluster::create($data);

        return redirect()->route('admin.clusters.index')->with('ok', 'کلستر جدید ایجاد شد.');
    }

    public function edit(int $id): View
    {
        return view('admin.clusters.form', ['cluster' => Cluster::findOrFail($id)]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $cluster = Cluster::findOrFail($id);
        $cluster->update($this->validated($request));

        return redirect()->route('admin.clusters.index')->with('ok', 'کلستر ویرایش شد.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $cluster = Cluster::findOrFail($id);

        if ($cluster->midwives()->exists()) {
            return back()->withErrors(['delete' => 'این کلستر قابله دارد و حذف نمی‌شود. ابتدا قابله‌ها را منتقل کنید.']);
        }

        $cluster->delete();

        return redirect()->route('admin.clusters.index')->with('ok', 'کلستر حذف شد.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'village' => ['required', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_m' => ['required', 'integer', 'min:20', 'max:5000'],
            'active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'نام کلستر را وارد کنید.',
            'village.required' => 'نام قریه را وارد کنید.',
            'lat.required' => 'موقعیت را روی نقشه انتخاب کنید.',
            'lat.between' => 'عرض جغرافیایی نامعتبر است.',
            'lng.required' => 'موقعیت را روی نقشه انتخاب کنید.',
            'radius_m.required' => 'شعاع ساحه را وارد کنید.',
        ]) + ['active' => $request->boolean('active')];
    }

    private function nextCode(): string
    {
        $max = (int) Cluster::max('id');

        return 'C-'.str_pad((string) ($max + 1), 2, '0', STR_PAD_LEFT);
    }
}
