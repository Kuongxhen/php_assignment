<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
<div id="cursor"></div>

<section class="hero hero--split">
	<div class="wrap hero__split">
		<div class="panel panel--patient">
			<h1 class="display">Patient Portal</h1>
			<p class="lede">Manage your profile and book appointments.</p>
			<form method="POST" action="<?php echo e(route('patient.login')); ?>" class="form">
				<?php echo csrf_field(); ?>
				<label class="field"><span>Email</span><input type="email" name="email" required></label>
				<label class="field"><span>Password</span><input type="password" name="password" required></label>
				<div class="row">
					<button type="submit" class="btn btn--gold">Sign In</button>
					<a class="btn btn--ghost" href="<?php echo e(route('patient.register')); ?>">I’m new</a>
				</div>
			</form>
		</div>
		<div class="panel panel--staff">
			<h2 class="title">Staff Access</h2>
			<p class="muted">For authorized personnel.</p>
			<form method="POST" action="<?php echo e(route('staffmod.login')); ?>" class="form">
				<?php echo csrf_field(); ?>
				<label class="field"><span>Staff Email</span><input type="email" name="email" required></label>
				<label class="field"><span>Password</span><input type="password" name="password" required></label>
				<label class="field"><span>Role</span>
					<select name="role" required>
						<option value="admin">Admin</option>
						<option value="doctor">Doctor</option>
						<option value="receptionist">Receptionist</option>
					</select>
				</label>
				<div class="row">
					<button type="submit" class="btn btn--line">Sign In</button>
				</div>
			</form>
		</div>
	</div>
</section>

<section class="section" id="care">
	<div class="wrap cards">
		<article class="card"><h3>Appointments</h3><p>Request visits with effortless ease. We keep time with you.</p></article>
		<article class="card"><h3>Records</h3><p>Allergies, medication, and history — elegantly organized.</p></article>
		<article class="card"><h3>Privacy</h3><p>Quietly secure. Your information stays yours.</p></article>
	</div>
</section>

<section class="login" id="patient-login">
	<div class="wrap narrow">
		<div class="auth">
			<header class="auth__head"><h4>Patient Access</h4><p>Welcome back. Sign in to continue.</p></header>
			<form method="POST" action="<?php echo e(route('patient.login')); ?>" class="form">
				<?php echo csrf_field(); ?>
				<label class="field"><span>Email</span><input type="email" name="email" required></label>
				<label class="field"><span>Password</span><input type="password" name="password" required></label>
				<div class="form__row"><button type="submit" class="btn btn--gold">Sign In</button><a href="<?php echo e(route('patient.register')); ?>" class="link link--soft">Create account</a></div>
			</form>
		</div>
	</div>
</section>

<footer class="footer" id="contact">
	<div class="wrap foot__grid"><div class="smallprint"><p>© Clinique Aurelia. All rights reserved.</p></div><nav class="foot__links"><a class="link" href="#journal">Journal</a><a class="link" href="#careers">Careers</a><a class="link" href="#privacy">Privacy</a><a class="link" href="#terms">Terms</a></nav></div>
</footer>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace;min-height:100vh}
	.wrap{max-width:1200px;margin:0 auto;padding:0 32px}
	/* nav/buttons/fields styles remain from previous section */
	.hf-nav{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.7);backdrop-filter:blur(10px);border-bottom:1px solid var(--line)}
	.nav__bar{display:flex;align-items:center;justify-content:space-between;height:76px}
	.brand{font-weight:900;letter-spacing:.02em;text-decoration:none;color:var(--ink)}
	.brand span{margin-left:.2ch;color:var(--gold)}
	.nav__links{display:flex;gap:24px}
	.link{color:#3b3b3b;text-decoration:none;border-bottom:1px solid transparent;padding-bottom:2px;transition:color .2s,border-color .2s,opacity .2s}
	.link:hover{color:#000;border-color:var(--line)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
	.btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.btn--gold{background:linear-gradient(180deg,var(--gold),var(--gold-2));color:#141414;box-shadow:0 16px 42px rgba(198,167,107,.18)}
	.btn--ghost{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.field{display:grid;gap:6px}
	.field span{font-size:12px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.field input{height:44px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 12px}
	.field input:focus{outline:none;border-color:rgba(198,167,107,.5)}

	.hero.hero--split{min-height:calc(100vh - 76px);display:flex;align-items:center;border-bottom:1px solid var(--line)}
	.hero__split{display:grid;grid-template-columns:1fr 1fr;gap:24px;width:100%}
	.panel{border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(0,0,0,.06);padding:24px}
	.panel--patient .display{margin:0 0 6px;font-size:34px}
	.panel--staff .title{margin:0 0 6px;font-size:22px}
	.lede{margin:8px 0 16px;color:var(--muted)}
	.row{display:flex;gap:10px;flex-wrap:wrap}

	.section{padding:60px 0}
	.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
	.card{padding:26px;border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.04)}

	.login .auth{padding:28px;border-radius:20px;border:1px solid var(--line);background:#fff;box-shadow:0 12px 34px rgba(0,0,0,.06)}
	.auth__head .muted{color:var(--muted)}

	.footer{border-top:1px solid var(--line);padding:30px 0 40px;background:linear-gradient(180deg,rgba(255,255,255,.7),rgba(255,255,255,.95))}
	.foot__grid{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
	.smallprint{color:#6a6a6a}
	.foot__links{display:flex;gap:18px}
	@media(max-width: 980px){.hero__split{grid-template-columns:1fr}.cards{grid-template-columns:1fr}}
</style>

<script>
	(function(){
		const cursor=document.getElementById('cursor');let raf,tx=0,ty=0,cx=0,cy=0;function loop(){cx+=(tx-cx)*0.18;cy+=(ty-cy)*0.18;cursor.style.transform=`translate(${cx}px, ${cy}px)`;raf=requestAnimationFrame(loop)}
		document.addEventListener('mousemove',e=>{tx=e.clientX-9;ty=e.clientY-9;if(!raf)raf=requestAnimationFrame(loop)});
		const hot=el=>['A','BUTTON','INPUT','LABEL'].includes(el.tagName)||el.classList?.contains('btn')||el.classList?.contains('card')||el.classList?.contains('link');
		document.addEventListener('mouseover',e=>{if(hot(e.target))cursor.classList.add('hover')});
		document.addEventListener('mouseout',e=>{if(hot(e.target))cursor.classList.remove('hover')});
		document.addEventListener('mousedown',()=>{cursor.classList.add('click');setTimeout(()=>cursor.classList.remove('click'),280)});
	})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/home.blade.php ENDPATH**/ ?>