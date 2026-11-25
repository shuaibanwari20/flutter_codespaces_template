<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'پنل مدیریت'); ?> — حضور قابله</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
    <?php echo $__env->yieldContent('head'); ?>
</head>
<body>
<header class="site-header">
    <div class="inner">
        <div class="brand">
            <div class="logo">ح</div>
            <div>
                حضور قابله
                <small>پنل مدیریت موسسه — 42 کلستر</small>
            </div>
        </div>
        <nav class="main-nav">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">امروز (داشبورد)</a>
            <a href="<?php echo e(route('admin.map')); ?>" class="<?php echo e(request()->routeIs('admin.map') ? 'active' : ''); ?>">نقشه کلسترها</a>
            <a href="<?php echo e(route('admin.reports')); ?>" class="<?php echo e(request()->routeIs('admin.reports') ? 'active' : ''); ?>">راپورها</a>
            <a href="<?php echo e(route('admin.clusters.index')); ?>" class="<?php echo e(request()->routeIs('admin.clusters.*') ? 'active' : ''); ?>">مدیریت کلسترها</a>
            <a href="<?php echo e(route('admin.midwives.index')); ?>" class="<?php echo e(request()->routeIs('admin.midwives.*') ? 'active' : ''); ?>">مدیریت قابله‌ها</a>
            <a href="<?php echo e(route('admin.leaves.index')); ?>" class="<?php echo e(request()->routeIs('admin.leaves.*') ? 'active' : ''); ?>">رخصتی‌ها</a>
            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="<?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>">اعلان‌ها</a>
            <a href="<?php echo e(route('admin.problems.index')); ?>" class="<?php echo e(request()->routeIs('admin.problems.*') ? 'active' : ''); ?>">گزارش مشکلات</a>
            <a href="<?php echo e(route('admin.settings')); ?>" class="<?php echo e(request()->routeIs('admin.settings') ? 'active' : ''); ?>">تنظیمات</a>
            <form method="POST" action="<?php echo e(route('admin.logout')); ?>" class="inline-form">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-gold btn-sm">خروج</button>
            </form>
        </nav>
    </div>
</header>

<main class="container">
    <?php if(session('ok')): ?>
        <div class="alert alert-ok"><?php echo e(session('ok')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-err">
            لطفاً خطاهای زیر را بررسی کنید:
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>

    <div class="footer-note">
        سیستم ثبت حضور قابله‌ها — اوقات کاری: 8:00 صبح (وقت کابل) · تاخیر تا 8:30 ·
        تاریخ‌ها میلادی (عیسوی) — <?php echo e(\App\Support\DariDate::full(\App\Support\AttendanceService::now())); ?>

    </div>
</main>

<?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/hatch/workspace/projects/midwife-attendance/arena-build/midwife-attendance/backend/resources/views/admin/layout.blade.php ENDPATH**/ ?>