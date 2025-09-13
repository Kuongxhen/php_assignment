<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Clinic Portal'); ?></title>
    <style>
        :root{--paper:#fbfaf7;--ink:#1b1b1b;--muted:#595959;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
        html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace;font-size:18px;line-height:1.6}
        h1,h2,h3{line-height:1.25}
        .wrap{max-width:1200px;margin:0 auto;padding:0 24px}
        .nav{position:sticky;top:0;z-index:50;background:rgba(251,250,247,.9);backdrop-filter:saturate(180%) blur(8px);border-bottom:1px solid var(--line)}
        .nav__in{display:flex;align-items:center;justify-content:space-between;gap:16px;height:64px}
        .nav__links{display:flex;gap:14px;align-items:center}
        .link{color:var(--ink);text-decoration:none;padding:8px 12px;border-radius:10px;border:1px solid transparent}
        .link:hover{border-color:var(--line)}
        .btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
        .btn--gold{background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414}
        .btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
        input,select,textarea{font-size:18px}
    </style>
</head>
<body>
    <?php 
        $authUser = auth()->user(); 
        $isPatientContext = $authUser && $authUser->role === 'patient' && request()->routeIs('patient.*') && !request()->routeIs('patient.register') && !request()->routeIs('patient.login');
        $isPublicPage = request()->routeIs('home') || request()->routeIs('products');
    ?>
    <?php if($isPublicPage): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('home')); ?>" style="font-weight:900;letter-spacing:.02em;color:var(--ink)">
                    Clinique<span style="margin-left:.2ch;color:var(--gold)">Aurelia</span>
                </a>
                <a class="link" href="<?php echo e(route('home')); ?>#care">Care</a>
                <a class="link" href="<?php echo e(route('products')); ?>" style="<?php echo e(request()->routeIs('products') ? 'color:var(--gold);font-weight:700' : ''); ?>">Products</a>
                <a class="link" href="<?php echo e(route('home')); ?>#consultants">Consultants</a>
                <a class="link" href="<?php echo e(route('home')); ?>#journal">Journal</a>
                <a class="link" href="<?php echo e(route('home')); ?>#contact">Contact</a>
            </div>
            <div class="nav__links">
                <a href="<?php echo e(route('patient.register')); ?>" class="btn btn--line">Create Account</a>
            </div>
        </div>
    </nav>
    <?php elseif($isPatientContext): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('patient.dashboard')); ?>">Dashboard</a>
                <a class="link" href="<?php echo e(route('patient.profile.show')); ?>">Profile</a>
                <a class="link" href="<?php echo e(route('patient.appointments.index')); ?>">Appointments</a>
                <a class="link" href="<?php echo e(route('patient.messages.index')); ?>">Messages</a>
            </div>
            <div class="nav__links">
                <a class="link" href="#" onclick="if(history.length>1){history.back();}else{window.location='<?php echo e(route('patient.dashboard')); ?>';}return false;">Back</a>
                <form method="POST" action="<?php echo e(route('patient.logout')); ?>" style="margin:0">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn--line" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <?php elseif(request()->routeIs('patient.register')): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('home')); ?>">Home</a>
                <a class="link" href="<?php echo e(url('#care')); ?>" onclick="event.preventDefault();window.location='<?php echo e(route('home')); ?>#care'">Care</a>
                <a class="link" href="<?php echo e(route('products')); ?>">Products</a>
                <a class="link" href="<?php echo e(url('#consultants')); ?>" onclick="event.preventDefault();window.location='<?php echo e(route('home')); ?>#consultants'">Consultants</a>
                <a class="link" href="<?php echo e(url('#contact')); ?>" onclick="event.preventDefault();window.location='<?php echo e(route('home')); ?>#contact'">Contact</a>
            </div>
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('home')); ?>">Back</a>
            </div>
        </div>
    </nav>
    <?php elseif(auth()->check() && auth()->user()->role === 'admin'): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('home')); ?>">Home</a>
                <a class="link" href="<?php echo e(route('staffmod.admin.dashboard')); ?>">Staff Admin</a>
            </div>
            <div class="nav__links">
                <a class="link" href="#" onclick="if(history.length>1){history.back();}else{window.location='<?php echo e(route('home')); ?>';}return false;">Back</a>
            </div>
        </div>
    </nav>
    <?php elseif(session('user') && session('user_role') === 'receptionist'): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('staffmod.receptionist.dashboard')); ?>">Receptionist</a>
                <a class="link" href="<?php echo e(route('staffmod.receptionist.appointments')); ?>">Appointments</a>
                <a class="link" href="<?php echo e(route('staffmod.receptionist.inbox')); ?>">Messages</a>
            </div>
            <div class="nav__links">
                <form method="POST" action="<?php echo e(route('staffmod.logout')); ?>" style="margin:0"><?php echo csrf_field(); ?><button class="btn btn--line">Logout</button></form>
            </div>
        </div>
    </nav>
    <?php elseif(session('user') && session('user_role') === 'doctor'): ?>
    <nav class="nav">
        <div class="wrap nav__in">
            <div class="nav__links">
                <a class="link" href="<?php echo e(route('staffmod.doctor.dashboard')); ?>">Doctor</a>
                <a class="link" href="<?php echo e(route('staffmod.doctor.schedule')); ?>">Leave</a>
            </div>
            <div class="nav__links">
                <form method="POST" action="<?php echo e(route('staffmod.logout')); ?>" style="margin:0"><?php echo csrf_field(); ?><button class="btn btn--line">Logout</button></form>
            </div>
        </div>
    </nav>
    <?php endif; ?>
                    <?php echo $__env->yieldContent('content'); ?>
</body>
</html><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/layouts/app.blade.php ENDPATH**/ ?>