@extends('admin.layout')

@section('title', 'درخواست‌های رخصتی')

@section('content')
<div class="page-title">
    <div>
        <h1>درخواست‌های رخصتی</h1>
        <div class="sub">تایید یا رد درخواست‌های رخصتی قابله‌ها — تاریخ‌ها میلادی</div>
    </div>
</div>

<div class="pill-tabs">
    <a href="{{ route('admin.leaves.index') }}" class="{{ $status === null ? 'active' : '' }}">همه</a>
    <a href="{{ route('admin.leaves.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">در انتظار</a>
    <a href="{{ route('admin.leaves.index', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'active' : '' }}">تایید شده</a>
    <a href="{{ route('admin.leaves.index', ['status' => 'rejected']) }}" class="{{ $status === 'rejected' ? 'active' : '' }}">رد شده</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>قابله</th>
                <th>کلستر</th>
                <th>از تاریخ</th>
                <th>تا تاریخ</th>
                <th>روزها</th>
                <th>دلیل</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($leaves as $leave)
                <tr>
                    <td style="font-weight: 700;">{{ $leave->midwife->full_name ?? '—' }}
                        <span class="muted small">{{ \App\Support\DariDate::en($leave->midwife->code ?? '') }}</span>
                    </td>
                    <td>{{ $leave->midwife->cluster ? \App\Support\DariDate::en($leave->midwife->cluster->code) : '—' }}</td>
                    <td class="num">{{ \App\Support\DariDate::dayMonthYear($leave->from_date) }}</td>
                    <td class="num">{{ \App\Support\DariDate::dayMonthYear($leave->to_date) }}</td>
                    <td class="num">{{ \App\Support\DariDate::en($leave->durationDays()) }}</td>
                    <td class="small">{{ $leave->reason }}</td>
                    <td>
                        @if($leave->status === 'pending')
                            <span class="chip chip-amber">در انتظار</span>
                        @elseif($leave->status === 'approved')
                            <span class="chip chip-green">تایید شده</span>
                        @else
                            <span class="chip chip-red">رد شده</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($leave->status === 'pending')
                            <form method="POST" action="{{ route('admin.leaves.approve', $leave->id) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm"
                                        onclick="return confirm('این رخصتی تایید شود؟ از باقی‌مانده رخصتی قابله کم می‌شود.');">تایید</button>
                            </form>
                            <form method="POST" action="{{ route('admin.leaves.reject', $leave->id) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('این درخواست رد شود؟');">رد</button>
                            </form>
                        @else
                            <span class="muted small">{{ $leave->review_note ?? '—' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted text-center">درخواستی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
