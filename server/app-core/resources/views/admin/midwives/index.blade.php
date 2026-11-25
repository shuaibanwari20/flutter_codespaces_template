@extends('admin.layout')

@section('title', 'مدیریت قابله‌ها')

@section('content')
<div class="page-title">
    <div>
        <h1>مدیریت قابله‌ها</h1>
        <div class="sub">{{ \App\Support\DariDate::en($midwives->count()) }} قابله — تنظیم پین، قطع اتصال دستگاه، ویرایش</div>
    </div>
    <div class="actions">
        <a class="btn btn-gold btn-sm" href="{{ route('admin.midwives.create') }}">+ قابله جدید</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>کد</th>
                <th>نام</th>
                <th>تلیفون</th>
                <th>کلستر</th>
                <th>دستگاه متصل</th>
                <th>باقی‌مانده رخصتی</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($midwives as $midwife)
                <tr>
                    <td class="num">{{ \App\Support\DariDate::en($midwife->code) }}</td>
                    <td style="font-weight: 700;">{{ $midwife->full_name }}</td>
                    <td class="num">{{ \App\Support\DariDate::en($midwife->phone ?? '—') }}</td>
                    <td>
                        @if($midwife->cluster)
                            <span class="chip chip-teal">{{ \App\Support\DariDate::en($midwife->cluster->code) }} — {{ $midwife->cluster->village }}</span>
                        @else
                            <span class="chip chip-grey">—</span>
                        @endif
                    </td>
                    <td>
                        @if($midwife->device_id)
                            <span class="chip chip-green">متصل</span>
                            <form method="POST" action="{{ route('admin.midwives.unbind', $midwife->id) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('اتصال دستگاه قطع شود؟');">قطع دستگاه</button>
                            </form>
                        @else
                            <span class="chip chip-grey">ندارد</span>
                        @endif
                    </td>
                    <td class="num">{{ \App\Support\DariDate::en($midwife->leave_balance) }} روز</td>
                    <td>
                        @if($midwife->active)
                            <span class="chip chip-green">فعال</span>
                        @else
                            <span class="chip chip-red">غیرفعال</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        <a class="btn btn-ghost btn-sm" href="{{ route('admin.midwives.edit', $midwife->id) }}">ویرایش</a>
                        <form method="POST" action="{{ route('admin.midwives.reset-pin', $midwife->id) }}" class="inline-form">
                            @csrf
                            <input class="form-control" style="width: 76px; display: inline-block; padding: 6px 8px;"
                                   name="pin" placeholder="پین جدید" maxlength="8" required>
                            <button type="submit" class="btn btn-gold btn-sm">تغییر پین</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
