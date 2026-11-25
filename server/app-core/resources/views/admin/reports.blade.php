@extends('admin.layout')

@section('title', 'راپورهای حضور')

@section('content')
<div class="page-title">
    <div>
        <h1>راپورهای حضور و غیاب</h1>
        <div class="sub">فیلتر بر اساس تاریخ میلادی، کلستر، قابله و وضعیت — خروجی Excel و CSV</div>
    </div>
    <div class="actions">
        <a class="btn btn-success btn-sm" href="{{ route('admin.reports.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}">
            ⬇ دانلود Excel (.xlsx)
        </a>
        <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
            ⬇ دانلود CSV
        </a>
    </div>
</div>

<div class="card">
    <h2>فیلترها</h2>
    <form method="GET" action="{{ route('admin.reports') }}">
        <div class="form-grid">
            <div>
                <label class="field-label">از تاریخ</label>
                <input class="form-control" type="date" name="from" value="{{ $filters['from'] }}">
            </div>
            <div>
                <label class="field-label">تا تاریخ</label>
                <input class="form-control" type="date" name="to" value="{{ $filters['to'] }}">
            </div>
            <div>
                <label class="field-label">کلستر</label>
                <select class="form-control" name="cluster_id">
                    <option value="">همه کلسترها</option>
                    @foreach($clusters as $cluster)
                        <option value="{{ $cluster->id }}" @selected($filters['cluster_id'] == $cluster->id)>
                            {{ \App\Support\DariDate::en($cluster->code) }} — {{ $cluster->village }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">قابله</label>
                <select class="form-control" name="midwife_id">
                    <option value="">همه قابله‌ها</option>
                    @foreach($midwives as $midwife)
                        <option value="{{ $midwife->id }}" @selected($filters['midwife_id'] == $midwife->id)>
                            {{ \App\Support\DariDate::en($midwife->code) }} — {{ $midwife->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">وضعیت</label>
                <select class="form-control" name="status">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="on_time" @selected($filters['status'] === 'on_time')>حاضر به‌وقت</option>
                    <option value="late" @selected($filters['status'] === 'late')>دیرکرد</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">اعمال فیلتر</button>
            <a class="btn btn-ghost" href="{{ route('admin.reports') }}">پاک کردن</a>
        </div>
    </form>
</div>

<div class="card">
    <h2>خلاصه ماهانه هر قابله (در بازه انتخاب‌شده)</h2>
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>کد</th>
                <th>نام قابله</th>
                <th>کلستر</th>
                <th>روز حاضر به‌وقت</th>
                <th>روز دیرکرد</th>
                <th>کل ثبت‌شده</th>
                <th>درصد به‌وقت</th>
            </tr>
            </thead>
            <tbody>
            @foreach($summaryRows as $row)
                <tr>
                    <td class="num">{{ \App\Support\DariDate::en($row['midwife']->code) }}</td>
                    <td style="font-weight: 700;">{{ $row['midwife']->full_name }}</td>
                    <td>{{ $row['midwife']->cluster ? \App\Support\DariDate::en($row['midwife']->cluster->code) : '—' }}</td>
                    <td class="num" style="color: #27875C;">{{ \App\Support\DariDate::en($row['on_time']) }}</td>
                    <td class="num" style="color: #B7791F;">{{ \App\Support\DariDate::en($row['late']) }}</td>
                    <td class="num">{{ \App\Support\DariDate::en($row['total']) }}</td>
                    <td class="num">
                        {{ \App\Support\DariDate::en($row['total'] > 0 ? (int) round($row['on_time'] / $row['total'] * 100) : 0) }}%
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>سوابق حضور (جزئیات)</h2>
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>تاریخ</th>
                <th>روز</th>
                <th>قابله</th>
                <th>کلستر</th>
                <th>ساعت ثبت</th>
                <th>وضعیت</th>
                <th>فاصله</th>
                <th>نوع</th>
                <th>کد پیگیری</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $a)
                <tr>
                    <td class="num">{{ \App\Support\DariDate::dayMonthYear($a->attendance_date) }}</td>
                    <td>{{ \App\Support\DariDate::weekday($a->attendance_date) }}</td>
                    <td style="font-weight: 700;">{{ $a->midwife->full_name ?? '—' }} <span class="muted small">{{ \App\Support\DariDate::en($a->midwife->code ?? '') }}</span></td>
                    <td>{{ $a->cluster ? \App\Support\DariDate::en($a->cluster->code) : '—' }}</td>
                    <td class="num">{{ \App\Support\DariDate::time($a->checked_at_server) }}</td>
                    <td>
                        @if($a->status === 'on_time')
                            <span class="chip chip-green">حاضر به‌وقت</span>
                        @else
                            <span class="chip chip-amber">دیرکرد {{ \App\Support\DariDate::en($a->late_minutes) }} دقیقه</span>
                        @endif
                    </td>
                    <td class="num">{{ \App\Support\DariDate::en((string) round((float) $a->distance_m)) }} متر</td>
                    <td>
                        @if($a->offline)
                            <span class="chip chip-grey">آفلاین</span>
                        @else
                            <span class="chip chip-teal">آنلاین</span>
                        @endif
                    </td>
                    <td class="num">{{ \App\Support\DariDate::en($a->tracking_code) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="muted text-center">هیچ رکوردی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 14px;">{{ $rows->links() }}</div>
</div>
@endsection
