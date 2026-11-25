@extends('admin.layout')

@section('title', 'مدیریت کلسترها')

@section('content')
<div class="page-title">
    <div>
        <h1>مدیریت کلسترها</h1>
        <div class="sub">{{ \App\Support\DariDate::en($clusters->count()) }} کلستر ثبت‌شده — ویرایش، ایجاد و حذف</div>
    </div>
    <div class="actions">
        <a class="btn btn-gold btn-sm" href="{{ route('admin.clusters.create') }}">+ کلستر جدید</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>کد</th>
                <th>نام</th>
                <th>قریه</th>
                <th>ولسوالی / ولایت</th>
                <th>موقعیت (Lat, Lng)</th>
                <th>شعاع</th>
                <th>قابله‌ها</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clusters as $cluster)
                <tr>
                    <td class="num">{{ \App\Support\DariDate::en($cluster->code) }}</td>
                    <td style="font-weight: 700;">{{ $cluster->name }}</td>
                    <td>{{ $cluster->village }}</td>
                    <td class="muted">{{ $cluster->district }} / {{ $cluster->province }}</td>
                    <td class="num small">{{ \App\Support\DariDate::en(number_format($cluster->lat, 4, '.', '')) }}, {{ \App\Support\DariDate::en(number_format($cluster->lng, 4, '.', '')) }}</td>
                    <td class="num">{{ \App\Support\DariDate::en($cluster->radius_m) }} متر</td>
                    <td class="num">{{ \App\Support\DariDate::en($cluster->midwives_count) }}</td>
                    <td>
                        @if($cluster->active)
                            <span class="chip chip-green">فعال</span>
                        @else
                            <span class="chip chip-grey">غیرفعال</span>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-ghost btn-sm" href="{{ route('admin.clusters.edit', $cluster->id) }}">ویرایش</a>
                        <form method="POST" action="{{ route('admin.clusters.destroy', $cluster->id) }}" class="inline-form"
                              onsubmit="return confirm('آیا مطمئن هستید که این کلستر حذف شود؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
