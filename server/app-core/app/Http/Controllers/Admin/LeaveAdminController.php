<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Support\DariDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $leaves = Leave::with('midwife.cluster')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($leave) {
                $leave->display_range = DariDate::dayMonthYear($leave->from_date).' تا '.DariDate::dayMonthYear($leave->to_date);

                return $leave;
            });

        return view('admin.leaves', ['leaves' => $leaves, 'status' => $status]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['leave' => 'این درخواست قبلاً بررسی شده است.']);
        }

        $leave->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'review_note' => $request->input('note'),
        ]);

        // Deduct leave balance (days inclusive)
        $midwife = $leave->midwife;
        if ($midwife) {
            $days = $leave->durationDays();
            $midwife->update(['leave_balance' => max(0, $midwife->leave_balance - $days)]);
        }

        return back()->with('ok', 'رخصتی تایید شد.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['leave' => 'این درخواست قبلاً بررسی شده است.']);
        }

        $leave->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'review_note' => $request->input('note'),
        ]);

        return back()->with('ok', 'درخواست رخصتی رد شد.');
    }
}
