<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Support\AttendanceService;
use App\Support\DariDate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /** GET /api/leaves — my leave requests with status. */
    public function index(Request $request): JsonResponse
    {
        $midwife = $request->user();

        $leaves = Leave::where('midwife_id', $midwife->id)
            ->orderByDesc('from_date')
            ->get()
            ->map(fn ($leave) => $this->payload($leave));

        return response()->json([
            'success' => true,
            'data' => [
                'leave_balance' => $midwife->leave_balance,
                'leaves' => $leaves,
            ],
        ]);
    }

    /** POST /api/leaves — submit a leave request (from/to Gregorian dates + reason). */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_date' => ['required', 'date_format:Y-m-d'],
            'to_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'from_date.required' => 'تاریخ شروع را انتخاب کنید.',
            'from_date.date_format' => 'تاریخ باید به شکل میلادی (YYYY-MM-DD) باشد.',
            'to_date.required' => 'تاریخ ختم را انتخاب کنید.',
            'to_date.date_format' => 'تاریخ باید به شکل میلادی (YYYY-MM-DD) باشد.',
            'to_date.after_or_equal' => 'تاریخ ختم باید بعد از تاریخ شروع باشد.',
            'reason.required' => 'دلیل رخصتی را بنویسید.',
            'reason.min' => 'دلیل رخصتی حداقل ۳ حرف باشد.',
        ]);

        $midwife = $request->user();
        $from = Carbon::parse($data['from_date'], AttendanceService::timezone())->startOfDay();
        $to = Carbon::parse($data['to_date'], AttendanceService::timezone())->startOfDay();

        if ($from->isPast() && ! $from->isToday()) {
            return response()->json([
                'success' => false,
                'message' => 'تاریخ شروع رخصتی نمی‌تواند در گذشته باشد.',
            ], 422);
        }

        $days = (int) $from->diffInDays($to) + 1;
        if ($days > 30) {
            return response()->json([
                'success' => false,
                'message' => 'درخواست رخصتی نمی‌تواند بیشتر از 30 روز باشد.',
            ], 422);
        }

        $leave = Leave::create([
            'midwife_id' => $midwife->id,
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'درخواست رخصتی شما ثبت شد و در انتظار تایید مدیریت است.',
            'data' => ['leave' => $this->payload($leave)],
        ]);
    }

    private function payload(Leave $leave): array
    {
        return [
            'id' => $leave->id,
            'from_date' => $leave->from_date->toDateString(),
            'to_date' => $leave->to_date->toDateString(),
            'from_display' => DariDate::dayMonthYear($leave->from_date),
            'to_display' => DariDate::dayMonthYear($leave->to_date),
            'days' => $leave->durationDays(),
            'reason' => $leave->reason,
            'status' => $leave->status,
            'status_label' => match ($leave->status) {
                'pending' => 'در انتظار',
                'approved' => 'تایید شده',
                'rejected' => 'رد شده',
            },
            'review_note' => $leave->review_note,
        ];
    }
}
