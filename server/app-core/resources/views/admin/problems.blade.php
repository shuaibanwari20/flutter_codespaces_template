@extends('admin.layout')

@section('title', 'گزارش مشکلات')

@section('content')
<div class="page-title">
    <div>
        <h1>گزارش مشکلات قابله‌ها</h1>
        <div class="sub">مشکلات ثبت‌شده از اپلیکیشن موبایل (GPS / اپلیکیشن / سایر)</div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>قابله</th>
                <th>کلستر</th>
                <th>نوع مشکل</th>
                <th>شرح</th>
                <th>تاریخ ثبت</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reports as $report)
                <tr>
                    <td style="font-weight: 700;">{{ $report->midwife->full_name ?? '—' }}
                        <span class="muted small">{{ \App\Support\DariDate::en($report->midwife->code ?? '') }}</span>
                    </td>
                    <td>{{ $report->midwife->cluster ? \App\Support\DariDate::en($report->midwife->cluster->code) : '—' }}</td>
                    <td>
                        @if($report->type === 'gps')
                            <span class="chip chip-gold">GPS</span>
                        @elseif($report->type === 'app')
                            <span class="chip chip-teal">اپلیکیشن</span>
                        @else
                            <span class="chip chip-grey">سایر</span>
                        @endif
                    </td>
                    <td class="small" style="max-width: 380px;">{{ $report->message }}</td>
                    <td class="num">{{ \App\Support\DariDate::dayMonthYear($report->created_at) }} — {{ \App\Support\DariDate::time24($report->created_at) }}</td>
                    <td>
                        @if($report->status === 'open')
                            <span class="chip chip-amber">باز</span>
                        @else
                            <span class="chip chip-green">حل شده</span>
                        @endif
                    </td>
                    <td>
                        @if($report->status === 'open')
                            <form method="POST" action="{{ route('admin.problems.resolve', $report->id) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">علامت‌گذاری حل‌شده</button>
                            </form>
                        @else
                            <span class="muted small">{{ $report->resolution_note ?? '—' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted text-center">گزارشی ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
