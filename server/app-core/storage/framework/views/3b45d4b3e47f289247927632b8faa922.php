<?php $__env->startSection('title', 'داشبورد امروز'); ?>

<?php $__env->startSection('head'); ?>
    <meta http-equiv="refresh" content="30">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-title">
    <div>
        <h1>داشبورد امروز</h1>
        <div class="sub"><?php echo e($todayLabel); ?> — <?php echo e($timeLabel); ?> (وقت کابل) · به‌روزرسانی خودکار هر 30 ثانیه</div>
    </div>
    <div class="actions">
        <a class="btn btn-ghost btn-sm" href="<?php echo e(route('admin.map')); ?>">نقشه کلسترها</a>
        <a class="btn btn-gold btn-sm" href="<?php echo e(route('admin.reports')); ?>">راپورها</a>
    </div>
</div>

<div class="stats-row">
    <div class="stat-card stat-teal">
        <div class="icon-circle">👥</div>
        <div class="value"><?php echo e(\App\Support\DariDate::en($total)); ?></div>
        <div class="label">کل قابله‌ها</div>
    </div>
    <div class="stat-card stat-green">
        <div class="icon-circle">✓</div>
        <div class="value"><?php echo e(\App\Support\DariDate::en($counts['on_time'])); ?></div>
        <div class="label">حاضر به‌وقت</div>
    </div>
    <div class="stat-card stat-amber">
        <div class="icon-circle">⏱</div>
        <div class="value"><?php echo e(\App\Support\DariDate::en($counts['late'])); ?></div>
        <div class="label">دیرکرد</div>
    </div>
    <div class="stat-card stat-red">
        <div class="icon-circle">✕</div>
        <div class="value"><?php echo e(\App\Support\DariDate::en($counts['absent'])); ?></div>
        <div class="label">غایب / هنوز نرسیده</div>
    </div>
    <div class="stat-card stat-grey">
        <div class="icon-circle">🌴</div>
        <div class="value"><?php echo e(\App\Support\DariDate::en($counts['leave'])); ?></div>
        <div class="label">رخصتی</div>
    </div>
</div>

<div class="card">
    <h2>وضعیت زنده قابله‌ها</h2>
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>کد</th>
                <th>نام قابله</th>
                <th>کلستر</th>
                <th>ساعت ثبت حضور</th>
                <th>فاصله از مرکز</th>
                <th>وضعیت</th>
                <th>کد پیگیری</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($m = $row['midwife']); ?>
                <tr>
                    <td class="num"><?php echo e(\App\Support\DariDate::en($m->code)); ?></td>
                    <td style="font-weight: 700;"><?php echo e($m->full_name); ?></td>
                    <td>
                        <?php if($m->cluster): ?>
                            <span class="chip chip-teal"><?php echo e(\App\Support\DariDate::en($m->cluster->code)); ?> — <?php echo e($m->cluster->village); ?></span>
                        <?php else: ?>
                            <span class="chip chip-grey">بدون کلستر</span>
                        <?php endif; ?>
                    </td>
                    <td class="num"><?php echo e($row['time']); ?></td>
                    <td class="num"><?php echo e($row['distance']); ?></td>
                    <td>
                        <?php if($row['state'] === 'on_time'): ?>
                            <span class="chip chip-green"><span class="dot" style="background:#2FA36B"></span>حاضر به‌وقت</span>
                        <?php elseif($row['state'] === 'late'): ?>
                            <span class="chip chip-amber"><span class="dot" style="background:#D69E2E"></span>دیرکرد <?php echo e(\App\Support\DariDate::en($row['attendance']->late_minutes)); ?> دقیقه</span>
                        <?php elseif($row['state'] === 'leave'): ?>
                            <span class="chip chip-grey"><span class="dot" style="background:#9AA8AB"></span>رخصتی</span>
                        <?php elseif($row['state'] === 'friday'): ?>
                            <span class="chip chip-grey">روز رخصتی (جمعه)</span>
                        <?php elseif($row['state'] === 'not_arrived'): ?>
                            <span class="chip chip-red"><span class="dot" style="background:#D9534F"></span>هنوز نرسیده</span>
                        <?php else: ?>
                            <span class="chip chip-red"><span class="dot" style="background:#D9534F"></span>غایب</span>
                        <?php endif; ?>
                    </td>
                    <td class="num"><?php echo e($row['attendance'] ? \App\Support\DariDate::en($row['attendance']->tracking_code) : '—'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/hatch/workspace/projects/midwife-attendance/arena-build/midwife-attendance/backend/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>