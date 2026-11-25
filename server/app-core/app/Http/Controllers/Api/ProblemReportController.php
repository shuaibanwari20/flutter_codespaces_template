<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProblemReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblemReportController extends Controller
{
    /** POST /api/problem-reports */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:gps,app,other'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'type.required' => 'نوع مشکل را انتخاب کنید.',
            'type.in' => 'نوع مشکل نامعتبر است.',
            'message.required' => 'شرح مشکل را بنویسید.',
            'message.min' => 'شرح مشکل حداقل ۵ حرف باشد.',
        ]);

        $report = ProblemReport::create([
            'midwife_id' => $request->user()->id,
            'type' => $data['type'],
            'message' => $data['message'],
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'گزارش مشکل شما ثبت شد. تیم پشتیبانی بررسی می‌کند.',
            'data' => ['id' => $report->id],
        ]);
    }
}
