<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProblemAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.problems', [
            'reports' => ProblemReport::with('midwife.cluster')->orderByDesc('created_at')->get(),
        ]);
    }

    public function resolve(Request $request, int $id): RedirectResponse
    {
        $report = ProblemReport::findOrFail($id);
        $report->update([
            'status' => 'resolved',
            'resolution_note' => $request->input('note'),
        ]);

        return back()->with('ok', 'گزارش حل شده علامت‌گذاری شد.');
    }
}
