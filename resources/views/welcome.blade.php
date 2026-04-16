<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ArrowK — Sistema para distribuidores de bicicletas eléctricas</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<style>
/* ================================================================
   DESIGN TOKENS — Sistema completo light / dark
   ================================================================ */
:root {
  color-scheme: light dark;

  /* ── LIGHT ── */
  --bg:        #f7f5fa;
  --bg-2:      #ede9f4;
  --bg-3:      #e3dced;
  --bg-4:      #d8cfe6;

  --surface:   rgba(82,37,102,0.05);
  --surface-2: rgba(82,37,102,0.09);
  --surface-3: rgba(82,37,102,0.14);

  --border:    rgba(82,37,102,0.10);
  --border-2:  rgba(82,37,102,0.18);
  --border-3:  rgba(82,37,102,0.28);

  --text:      #1e1529;
  --text-2:    #4d3b61;
  --text-3:    #8a789a;

  /* ── BRAND PURPLE ── */
  --p1: #3d1a52;
  --p2: #7A3A8E;
  --p3: #AD74C3;
  --p4: #d4aadf;
  --p5: #f3e8f9;

  --primary: var(--p2);
  --accent:  var(--p3);

  /* ── STATUS ── */
  --green:  #16a34a;
  --yellow: #ca8a04;
  --red:    #dc2626;
  --blue:   #2563eb;

  /* ── NAV (light) ── */
  --nav-bg:      rgba(247,245,250,0.82);
  --nav-border:  rgba(82,37,102,0.16);

  /* ── MISC ── */
  --radius:    14px;
  --radius-sm: 8px;
  --serif: 'DM Serif Display', serif;
  --sans:  'Figtree', system-ui, sans-serif;

  /* ── SHADOW ── */
  --shadow-sm: 0 2px 8px rgba(82,37,102,0.10);
  --shadow-md: 0 8px 32px rgba(82,37,102,0.14);
  --shadow-lg: 0 24px 64px rgba(82,37,102,0.18);
}

/* ── DARK OVERRIDE ── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg:        #0d0b12;
    --bg-2:      #141020;
    --bg-3:      #1c1728;
    --bg-4:      #241f30;

    --surface:   rgba(173,116,195,0.05);
    --surface-2: rgba(173,116,195,0.09);
    --surface-3: rgba(173,116,195,0.14);

    --border:    rgba(173,116,195,0.10);
    --border-2:  rgba(173,116,195,0.16);
    --border-3:  rgba(173,116,195,0.26);

    --text:      #f0ebf7;
    --text-2:    #c4afd4;
    --text-3:    #7a6a8a;

    --p4: #c49ad4;
    --p5: #2a1a38;

    --green:  #22c55e;
    --yellow: #eab308;
    --red:    #ef4444;
    --blue:   #60a5fa;

    --nav-bg:     rgba(13,11,18,0.85);
    --nav-border: rgba(173,116,195,0.18);

    --shadow-sm: 0 2px 8px rgba(0,0,0,0.40);
    --shadow-md: 0 8px 32px rgba(0,0,0,0.50);
    --shadow-lg: 0 24px 64px rgba(0,0,0,0.65);
  }
}

/* ================================================================
   RESET
   ================================================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 15px;
  line-height: 1.65;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  transition: background 0.3s, color 0.3s;
}
a { color: inherit; text-decoration: none; }
button { cursor: pointer; font-family: var(--sans); border: none; background: none; }
svg { display: block; flex-shrink: 0; }
img { display: block; max-width: 100%; }

/* ================================================================
   CUSTOM CURSOR (desktop only)
   ================================================================ */
@media (hover: hover) and (pointer: fine) {
  * { cursor: none; }
  .cursor {
    position: fixed; z-index: 9998; pointer-events: none;
    width: 10px; height: 10px;
    background: var(--p3);
    border-radius: 50%;
    transform: translate(-50%,-50%);
    transition: width .2s, height .2s, background .2s;
    mix-blend-mode: multiply;
  }
  @media (prefers-color-scheme: dark) {
    .cursor { mix-blend-mode: screen; }
  }
  .cursor-ring {
    position: fixed; z-index: 9997; pointer-events: none;
    width: 36px; height: 36px;
    border: 1.5px solid rgba(173,116,195,0.5);
    border-radius: 50%;
    transform: translate(-50%,-50%);
    transition: all .12s ease;
  }
  body:has(a:hover) .cursor,
  body:has(button:hover) .cursor { width: 16px; height: 16px; }
  body:has(a:hover) .cursor-ring,
  body:has(button:hover) .cursor-ring { width: 52px; height: 52px; border-color: rgba(173,116,195,0.85); }
}
.cursor, .cursor-ring { display: none; }
@media (hover: hover) and (pointer: fine) {
  .cursor, .cursor-ring { display: block; }
}

/* ================================================================
   LOADER
   ================================================================ */
#loader {
  position: fixed; inset: 0; z-index: 9999;
  background: var(--bg);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 2.5rem;
  transition: opacity .7s ease, visibility .7s ease;
}
#loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.loader-wordmark {
  font-family: var(--serif); font-size: 1.75rem;
  letter-spacing: -.01em; color: var(--text);
  display: flex; align-items: center; gap: .65rem;
}
.loader-wordmark-icon {
  width: 34px; height: 34px; background: var(--p2); border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
}
.wheel-and-hamster { --dur:1s; position:relative; width:12em; height:12em; font-size:14px; }
.wheel,.hamster,.hamster div,.spoke { position:absolute; }
.wheel,.spoke { border-radius:50%; top:0; left:0; width:100%; height:100%; }
.wheel { background:radial-gradient(100% 100% at center,hsla(0,0%,60%,0) 47.8%,hsl(285,38%,46%) 48%,hsl(285,38%,46%) 52%,hsla(0,0%,60%,0) 52.2%); z-index:2; }
.hamster { animation:hamster var(--dur) ease-in-out infinite; top:50%; left:calc(50% - 3.5em); width:7em; height:3.75em; transform:rotate(4deg) translate(-0.8em,1.85em); transform-origin:50% 0; z-index:1; }
.hamster__head { animation:hamsterHead var(--dur) ease-in-out infinite; background:hsl(30,80%,62%); border-radius:70% 30% 0 100%/40% 25% 25% 60%; box-shadow:0 -0.25em 0 hsl(30,80%,74%) inset,0.75em -1.55em 0 hsl(30,80%,88%) inset; top:0; left:-2em; width:2.75em; height:2.5em; transform-origin:100% 50%; }
.hamster__ear { animation:hamsterEar var(--dur) ease-in-out infinite; background:hsl(0,70%,80%); border-radius:50%; box-shadow:-0.25em 0 hsl(30,80%,62%) inset; top:-0.25em; right:-0.25em; width:0.75em; height:0.75em; transform-origin:50% 75%; }
.hamster__eye { animation:hamsterEye var(--dur) linear infinite; background-color:hsl(0,0%,5%); border-radius:50%; top:0.375em; left:1.25em; width:0.5em; height:0.5em; }
.hamster__nose { background:hsl(0,80%,72%); border-radius:35% 65% 85% 15%/70% 50% 50% 30%; top:0.75em; left:0; width:0.2em; height:0.25em; }
.hamster__body { animation:hamsterBody var(--dur) ease-in-out infinite; background:hsl(30,80%,88%); border-radius:50% 30% 50% 30%/15% 60% 40% 40%; box-shadow:0.1em 0.75em 0 hsl(30,80%,62%) inset,0.15em -0.5em 0 hsl(30,80%,78%) inset; top:0.25em; left:2em; width:4.5em; height:3em; transform-origin:17% 50%; transform-style:preserve-3d; }
.hamster__limb--fr,.hamster__limb--fl { clip-path:polygon(0 0,100% 0,70% 80%,60% 100%,0% 100%,40% 80%); top:2em; left:0.5em; width:1em; height:1.5em; transform-origin:50% 0; }
.hamster__limb--fr { animation:hamsterFRLimb var(--dur) linear infinite; background:linear-gradient(hsl(30,80%,78%) 80%,hsl(0,80%,72%) 80%); transform:rotate(15deg) translateZ(-1px); }
.hamster__limb--fl { animation:hamsterFLLimb var(--dur) linear infinite; background:linear-gradient(hsl(30,80%,88%) 80%,hsl(0,80%,82%) 80%); transform:rotate(15deg); }
.hamster__limb--br,.hamster__limb--bl { border-radius:0.75em 0.75em 0 0; clip-path:polygon(0 0,100% 0,100% 30%,70% 90%,70% 100%,30% 100%,40% 90%,0% 30%); top:1em; left:2.8em; width:1.5em; height:2.5em; transform-origin:50% 30%; }
.hamster__limb--br { animation:hamsterBRLimb var(--dur) linear infinite; background:linear-gradient(hsl(30,80%,78%) 90%,hsl(0,80%,72%) 90%); transform:rotate(-25deg) translateZ(-1px); }
.hamster__limb--bl { animation:hamsterBLLimb var(--dur) linear infinite; background:linear-gradient(hsl(30,80%,88%) 90%,hsl(0,80%,82%) 90%); transform:rotate(-25deg); }
.hamster__tail { animation:hamsterTail var(--dur) linear infinite; background:hsl(0,80%,82%); border-radius:0.25em 50% 50% 0.25em; box-shadow:0 -0.2em 0 hsl(0,80%,72%) inset; top:1.5em; right:-0.5em; width:1em; height:0.5em; transform:rotate(30deg) translateZ(-1px); transform-origin:0.25em 0.25em; }
.spoke { animation:spoke var(--dur) linear infinite; background:radial-gradient(100% 100% at center,hsl(285,38%,46%) 4.8%,hsla(0,0%,40%,0) 5%),linear-gradient(hsla(0,0%,40%,0) 46.9%,hsl(285,33%,42%) 47% 52.9%,hsla(0,0%,40%,0) 53%) 50% 50%/99% 99% no-repeat; }
@keyframes hamster{from,to{transform:rotate(4deg) translate(-0.8em,1.85em)}50%{transform:rotate(0) translate(-0.8em,1.85em)}}
@keyframes hamsterHead{from,25%,50%,75%,to{transform:rotate(0)}12.5%,37.5%,62.5%,87.5%{transform:rotate(8deg)}}
@keyframes hamsterEye{from,90%,to{transform:scaleY(1)}95%{transform:scaleY(0)}}
@keyframes hamsterEar{from,25%,50%,75%,to{transform:rotate(0)}12.5%,37.5%,62.5%,87.5%{transform:rotate(12deg)}}
@keyframes hamsterBody{from,25%,50%,75%,to{transform:rotate(0)}12.5%,37.5%,62.5%,87.5%{transform:rotate(-2deg)}}
@keyframes hamsterFRLimb{from,25%,50%,75%,to{transform:rotate(50deg) translateZ(-1px)}12.5%,37.5%,62.5%,87.5%{transform:rotate(-30deg) translateZ(-1px)}}
@keyframes hamsterFLLimb{from,25%,50%,75%,to{transform:rotate(-30deg)}12.5%,37.5%,62.5%,87.5%{transform:rotate(50deg)}}
@keyframes hamsterBRLimb{from,25%,50%,75%,to{transform:rotate(-60deg) translateZ(-1px)}12.5%,37.5%,62.5%,87.5%{transform:rotate(20deg) translateZ(-1px)}}
@keyframes hamsterBLLimb{from,25%,50%,75%,to{transform:rotate(20deg)}12.5%,37.5%,62.5%,87.5%{transform:rotate(-60deg)}}
@keyframes hamsterTail{from,25%,50%,75%,to{transform:rotate(30deg) translateZ(-1px)}12.5%,37.5%,62.5%,87.5%{transform:rotate(10deg) translateZ(-1px)}}
@keyframes spoke{from{transform:rotate(0)}to{transform:rotate(-1turn)}}
.loader-progress { width:160px; height:2px; background:var(--surface-3); border-radius:2px; overflow:hidden; margin-top:.5rem; }
.loader-progress-fill { height:100%; background:var(--p2); animation:progFill 2.4s cubic-bezier(.4,0,.2,1) forwards; width:0; }
@keyframes progFill{0%{width:0}60%{width:72%}85%{width:91%}100%{width:100%}}
.loader-label { font-size:.7rem; letter-spacing:.14em; text-transform:uppercase; color:var(--text-3); margin-top:.5rem; }

/* ================================================================
   AMBIENT & NOISE
   ================================================================ */
.ambient { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
.ambient-grid {
  position:absolute; inset:0;
  background-image:
    linear-gradient(rgba(122,58,142,0.06) 1px,transparent 1px),
    linear-gradient(90deg,rgba(122,58,142,0.06) 1px,transparent 1px);
  background-size:64px 64px;
  mask-image:radial-gradient(ellipse 80% 60% at 50% 0%,black 20%,transparent 100%);
}
@media (prefers-color-scheme: dark) {
  .ambient-grid {
    background-image:
      linear-gradient(rgba(173,116,195,0.04) 1px,transparent 1px),
      linear-gradient(90deg,rgba(173,116,195,0.04) 1px,transparent 1px);
  }
}
.ambient-orb { position:absolute; border-radius:50%; filter:blur(120px); }
.ao1 { width:700px; height:600px; background:rgba(122,58,142,0.12); top:-250px; left:-200px; }
.ao2 { width:600px; height:500px; background:rgba(82,37,102,0.08); top:40%; right:-200px; }
.ao3 { width:500px; height:400px; background:rgba(173,116,195,0.06); bottom:-100px; left:25%; }
@media (prefers-color-scheme: dark) {
  .ao1 { background:rgba(122,58,142,0.2); }
  .ao2 { background:rgba(82,37,102,0.14); }
  .ao3 { background:rgba(173,116,195,0.07); }
}
.noise {
  position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.03;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:200px 200px;
}

/* ================================================================
   LAYOUT
   ================================================================ */
.container { max-width:1180px; margin:0 auto; padding:0 2rem; position:relative; z-index:1; }
section { position:relative; z-index:1; }

/* ================================================================
   REVEAL SYSTEM
   ================================================================ */
.rv { opacity:0; transform:translateY(28px); transition:opacity .7s ease,transform .7s ease; }
.rv.in { opacity:1; transform:translateY(0); }
.rv.d1 { transition-delay:.1s; } .rv.d2 { transition-delay:.2s; } .rv.d3 { transition-delay:.3s; } .rv.d4 { transition-delay:.4s; }
.rv-left { opacity:0; transform:translateX(-36px); transition:opacity .8s ease,transform .8s ease; }
.rv-left.in { opacity:1; transform:translateX(0); }
.rv-right { opacity:0; transform:translateX(36px); transition:opacity .8s ease,transform .8s ease; }
.rv-right.in { opacity:1; transform:translateX(0); }

/* ================================================================
   BUTTONS
   ================================================================ */
.btn-primary {
  display:inline-flex; align-items:center; gap:.5rem;
  padding:.78rem 1.65rem;
  background:var(--p2); color:#fff;
  border-radius:100px; font-size:.875rem; font-weight:500;
  transition:background .2s,transform .15s,box-shadow .2s;
  box-shadow:0 0 24px rgba(122,58,142,0.4),0 2px 8px rgba(0,0,0,0.2);
}
.btn-primary:hover { background:#8B45A0; transform:translateY(-2px); box-shadow:0 0 40px rgba(122,58,142,0.55); }
.btn-primary:active { transform:translateY(0); }
.btn-ghost {
  display:inline-flex; align-items:center; gap:.45rem;
  padding:.78rem 1.4rem;
  background:var(--surface); color:var(--text-2);
  border:1px solid var(--border-3); border-radius:100px;
  font-size:.875rem; font-weight:450;
  transition:all .2s;
}
.btn-ghost:hover { background:var(--surface-2); color:var(--text); border-color:var(--p3); }

/* ================================================================
   NAVBAR
   ================================================================ */
nav {
  position:fixed; top:1.25rem; left:50%; transform:translateX(-50%);
  z-index:200; width:calc(100% - 3rem); max-width:1100px;
  display:flex; align-items:center; justify-content:space-between;
  padding:.65rem 1.25rem;
  background:var(--nav-bg);
  backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px);
  border:1px solid var(--nav-border); border-radius:100px;
  transition:top .3s, background .3s, box-shadow .3s;
  box-shadow: var(--shadow-sm);
}
nav.scrolled {
  top:.5rem;
  box-shadow: var(--shadow-md);
}
.nav-logo {
  display:flex; align-items:center; gap:.6rem;
  font-family:var(--serif); font-size:1.2rem; color:var(--text);
  white-space: nowrap;
}
.nav-logo-mark {
  width:28px; height:28px; background:var(--p2); border-radius:7px;
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.nav-links { display:flex; align-items:center; gap:1.75rem; list-style:none; }
.nav-links a { font-size:.84rem; color:var(--text-2); font-weight:450; transition:color .2s; white-space:nowrap; }
.nav-links a:hover { color:var(--text); }
.nav-right { display:flex; align-items:center; gap:.6rem; }
.nav-cta {
  padding:.45rem 1.1rem; background:var(--p2); color:#fff;
  border-radius:100px; font-size:.8rem; font-weight:500;
  transition:background .2s,transform .15s;
  box-shadow:0 0 16px rgba(122,58,142,0.35);
  white-space:nowrap;
}
.nav-cta:hover { background:#8B45A0; transform:scale(1.02); }
.nav-back {
  display:flex; align-items:center; gap:.35rem;
  font-size:.75rem; color:var(--text-3);
  padding:.35rem .8rem;
  border:1px solid var(--border-2); border-radius:100px;
  transition:all .2s; white-space:nowrap;
}
.nav-back:hover { color:var(--text-2); border-color:var(--border-3); background:var(--surface); }

/* Hamburger */
.nav-hamburger {
  display:none; flex-direction:column; gap:4.5px;
  padding:7px; background:var(--surface); border:1px solid var(--border-2);
  border-radius:8px;
}
.nav-hamburger span {
  display:block; width:18px; height:1.5px;
  background:var(--text-2); border-radius:2px;
  transition:all .28s cubic-bezier(.4,0,.2,1); transform-origin:center;
}
.nav-hamburger.open span:nth-child(1) { transform:translateY(6px) rotate(45deg); }
.nav-hamburger.open span:nth-child(2) { opacity:0; transform:scaleX(0); }
.nav-hamburger.open span:nth-child(3) { transform:translateY(-6px) rotate(-45deg); }

/* Mobile menu */
.mobile-menu {
  position:fixed; top:0; left:0; right:0; z-index:190;
  background:var(--bg); border-bottom:1px solid var(--border-2);
  padding:5.5rem 1.5rem 1.75rem;
  transform:translateY(-110%);
  transition:transform .35s cubic-bezier(.4,0,.2,1);
  pointer-events:none;
  box-shadow: var(--shadow-lg);
}
.mobile-menu.open { transform:translateY(0); pointer-events:all; }
.mobile-menu-links { display:flex; flex-direction:column; gap:0; margin-bottom:1.25rem; }
.mobile-menu-links a {
  font-size:.975rem; font-weight:500; color:var(--text-2);
  padding:.8rem .5rem; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between;
  transition:color .2s;
}
.mobile-menu-links a:last-child { border-bottom:none; }
.mobile-menu-links a:hover { color:var(--text); }
.mobile-menu-footer { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; }
.mobile-menu-footer button { justify-content:center; }

/* ================================================================
   HERO
   ================================================================ */
.hero {
  min-height:100svh; display:flex; align-items:center;
  padding:8rem 0 5rem; position:relative; overflow:hidden;
}
.hero-ring {
  position:absolute; right:-100px; top:50%; transform:translateY(-50%);
  width:560px; height:560px; pointer-events:none;
  animation:ringRotate 40s linear infinite;
  opacity:.12;
}
@media (prefers-color-scheme: dark) { .hero-ring { opacity:.08; } }
@keyframes ringRotate { to { transform:translateY(-50%) rotate(1turn); } }

.hero-grid {
  display:grid; grid-template-columns:1fr 1.05fr;
  gap:4rem; align-items:center;
}
.hero-badge {
  display:inline-flex; align-items:center; gap:.5rem;
  padding:.3rem .85rem;
  background:rgba(122,58,142,0.10); border:1px solid rgba(122,58,142,0.22);
  border-radius:100px; font-size:.74rem; color:var(--p2); font-weight:500;
  margin-bottom:1.75rem;
  animation:fadeUp .5s ease both;
}
@media (prefers-color-scheme: dark) {
  .hero-badge { color:var(--p4); }
}
.badge-dot { width:5px; height:5px; background:var(--p3); border-radius:50%; animation:pulse 2s ease-in-out infinite; }
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(1.7)}}

.hero h1 {
  font-family:var(--serif);
  font-size:clamp(2.6rem,5vw,4.6rem);
  line-height:1.06; letter-spacing:-.03em;
  color:var(--text);
  animation:fadeUp .55s .08s ease both;
}
.hero h1 em { font-style:italic; color:var(--p2); }
@media (prefers-color-scheme: dark) { .hero h1 em { color:var(--p3); } }

.hero-sub {
  margin-top:1.4rem; font-size:1rem; color:var(--text-2);
  line-height:1.8; max-width:500px; font-weight:350;
  animation:fadeUp .55s .16s ease both;
}
.hero-actions {
  display:flex; align-items:center; gap:1rem; margin-top:2.5rem;
  animation:fadeUp .55s .22s ease both;
  flex-wrap:wrap;
}
.hero-live {
  display:flex; align-items:center; gap:.6rem; margin-top:1.75rem;
  animation:fadeUp .55s .28s ease both;
}
.live-dot {
  width:7px; height:7px; background:var(--green); border-radius:50%;
  box-shadow:0 0 8px color-mix(in srgb,var(--green) 60%,transparent);
  animation:livePulse 2s ease-in-out infinite;
}
@keyframes livePulse{0%,100%{box-shadow:0 0 8px color-mix(in srgb,var(--green) 60%,transparent)}50%{box-shadow:0 0 18px color-mix(in srgb,var(--green) 80%,transparent)}}
.live-label { font-size:.78rem; color:var(--text-3); }
.live-count { font-size:.78rem; font-weight:600; color:var(--green); }

.hero-metrics {
  display:flex; gap:2.5rem; margin-top:2.5rem; padding-top:2rem;
  border-top:1px solid var(--border);
  animation:fadeUp .55s .34s ease both;
  flex-wrap:wrap;
}
.metric-val {
  font-family:var(--serif); font-size:1.9rem;
  letter-spacing:-.025em; line-height:1; color:var(--text);
}
.metric-label { font-size:.75rem; color:var(--text-3); margin-top:.28rem; }
@keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}

/* ================================================================
   DASHBOARD MOCKUP
   ================================================================ */
.hero-visual { position:relative; animation:fadeRight .8s .3s ease both; }
@keyframes fadeRight{from{opacity:0;transform:translateX(36px)}to{opacity:1;transform:translateX(0)}}

.float-card {
  position:absolute; z-index:3;
  background:var(--bg-2); border:1px solid var(--border-3);
  border-radius:14px; padding:.8rem 1rem;
  box-shadow:var(--shadow-md);
  pointer-events:none;
}
.float-card-a { top:-16px; right:-20px; animation:floatA 3.5s ease-in-out infinite; }
.float-card-b { bottom:28px; left:-20px; animation:floatB 4.2s ease-in-out infinite; }
@keyframes floatA{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-8px) rotate(.5deg)}}
@keyframes floatB{0%,100%{transform:translateY(0) rotate(.5deg)}50%{transform:translateY(8px) rotate(-1deg)}}
.fc-label { font-size:.62rem; color:var(--text-3); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.2rem; }
.fc-val { font-weight:600; font-size:1.05rem; letter-spacing:-.02em; }
.fc-val.green { color:var(--green); }
.fc-val.purple { color:var(--p2); }
@media (prefers-color-scheme: dark) { .fc-val.purple { color:var(--p4); } }

.mockup-shell {
  background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:18px; overflow:hidden;
  box-shadow:var(--shadow-lg), 0 0 60px rgba(122,58,142,0.10);
  position:relative;
}
.mockup-shell::after {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(122,58,142,0.55),transparent);
}
.m-topbar {
  padding:.65rem 1rem; background:var(--bg-3); border-bottom:1px solid var(--border);
  display:flex; align-items:center; gap:.65rem;
}
.m-dots { display:flex; gap:5px; }
.m-dot { width:9px; height:9px; border-radius:50%; }
.m-dot:nth-child(1){background:#ff5f57}.m-dot:nth-child(2){background:#febc2e}.m-dot:nth-child(3){background:#28c840}
.m-url-bar {
  flex:1; max-width:240px; margin:0 auto;
  background:var(--surface); border:1px solid var(--border); border-radius:4px;
  height:20px; display:flex; align-items:center; justify-content:center;
  font-size:.62rem; color:var(--text-3);
}
.m-body { display:grid; grid-template-columns:160px 1fr; min-height:380px; }
.m-sidebar {
  background:var(--bg-3); border-right:1px solid var(--border);
  padding:.9rem .6rem; display:flex; flex-direction:column; gap:2px;
}
.m-logo {
  display:flex; align-items:center; gap:.45rem;
  font-family:var(--serif); font-size:.9rem; color:var(--text);
  padding:.25rem .5rem .8rem;
}
.m-logo-mark {
  width:18px; height:18px; background:var(--p2); border-radius:4px;
  display:flex; align-items:center; justify-content:center;
}
.m-nav {
  display:flex; align-items:center; gap:.5rem;
  padding:.42rem .6rem; border-radius:6px;
  font-size:.68rem; color:var(--text-3);
  transition:all .15s; cursor:pointer;
}
.m-nav.active { background:rgba(122,58,142,0.14); color:var(--p2); }
@media (prefers-color-scheme: dark) { .m-nav.active { color:var(--p4); } }
.m-nav:hover:not(.active) { background:var(--surface); color:var(--text-2); }
.m-nav-ico { width:13px; height:13px; opacity:.75; }
.m-main { padding:1.1rem; overflow:hidden; }
.m-ptitle { font-size:.76rem; font-weight:600; margin-bottom:.8rem; letter-spacing:-.01em; }
.m-stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.5rem; margin-bottom:.8rem; }
.m-stat {
  background:var(--surface); border:1px solid var(--border);
  border-radius:8px; padding:.55rem .6rem;
}
.m-stat-l { font-size:.55rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-3); }
.m-stat-v { font-size:1.2rem; font-weight:700; margin-top:.1rem; letter-spacing:-.03em; }
.m-stat-c { font-size:.55rem; margin-top:.06rem; }
.m-bars-wrap { background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:.7rem; margin-bottom:.5rem; }
.m-bars-label { font-size:.58rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-3); margin-bottom:.5rem; }
.m-bars { display:flex; align-items:flex-end; gap:3px; height:52px; }
.m-bar {
  flex:1; border-radius:2px 2px 0 0;
  background:linear-gradient(180deg,var(--p2) 0%,rgba(122,58,142,0.2) 100%);
  transform-origin:bottom;
  animation:barGrow 1s cubic-bezier(.4,0,.2,1) both;
}
@keyframes barGrow{from{transform:scaleY(0)}to{transform:scaleY(1)}}
.m-bar:nth-child(1){animation-delay:.04s}.m-bar:nth-child(2){animation-delay:.08s}.m-bar:nth-child(3){animation-delay:.12s}.m-bar:nth-child(4){animation-delay:.16s}.m-bar:nth-child(5){animation-delay:.20s}.m-bar:nth-child(6){animation-delay:.24s}.m-bar:nth-child(7){animation-delay:.28s}.m-bar:nth-child(8){animation-delay:.32s}
.m-table-wrap { background:var(--surface); border:1px solid var(--border); border-radius:8px; overflow:hidden; }
.m-trow {
  display:flex; align-items:center; justify-content:space-between;
  padding:.32rem .6rem; border-bottom:1px solid var(--border);
  font-size:.62rem; color:var(--text-2); transition:background .15s;
}
.m-trow:last-child { border-bottom:none; }
.m-trow:hover { background:var(--surface-2); }
.m-trow-head { font-size:.56rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-3); background:var(--surface-2); }
.mbadge { padding:.1rem .4rem; border-radius:3px; font-size:.52rem; font-weight:600; }
.mbadge-y { background:rgba(202,138,4,0.12); color:var(--yellow); }
.mbadge-b { background:rgba(37,99,235,0.12); color:var(--blue); }
.mbadge-g { background:rgba(22,163,74,0.12); color:var(--green); }
@media (prefers-color-scheme: dark) {
  .mbadge-y { background:rgba(234,179,8,0.15); }
  .mbadge-b { background:rgba(96,165,250,0.15); }
  .mbadge-g { background:rgba(34,197,94,0.15); }
}

/* ================================================================
   TRUST STRIP
   ================================================================ */
.strip {
  border-top:1px solid var(--border); border-bottom:1px solid var(--border);
  padding:1.75rem 0; overflow:hidden; background:var(--bg-2);
}
.strip-track {
  display:flex; gap:3.5rem; align-items:center;
  animation:stripScroll 22s linear infinite;
  white-space:nowrap; width:max-content;
}
@keyframes stripScroll { to { transform:translateX(-50%); } }
.strip-label { font-size:.7rem; color:var(--text-3); text-transform:uppercase; letter-spacing:.1em; }
.strip-sep { width:4px; height:4px; border-radius:50%; background:var(--p3); opacity:.4; flex-shrink:0; }
.strip-item { font-size:.82rem; font-weight:500; color:var(--text-2); }

/* ================================================================
   EYEBROW / HEADINGS
   ================================================================ */
.eyebrow {
  display:inline-flex; align-items:center; gap:.5rem;
  font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.14em;
  color:var(--p2); margin-bottom:1rem;
}
@media (prefers-color-scheme: dark) { .eyebrow { color:var(--p3); } }
.eyebrow-line { width:20px; height:1px; background:currentColor; opacity:.6; }
h2 {
  font-family:var(--serif);
  font-size:clamp(1.9rem,4vw,2.9rem);
  letter-spacing:-.025em; line-height:1.12; color:var(--text);
}
h2 em { font-style:italic; color:var(--p2); }
@media (prefers-color-scheme: dark) { h2 em { color:var(--p3); } }
.sub { color:var(--text-2); font-size:.975rem; line-height:1.8; max-width:500px; margin-top:.75rem; font-weight:300; }

/* ================================================================
   PROBLEM / SOLUTION
   ================================================================ */
.ps { padding:8rem 0; }
.ps-header { text-align:center; margin-bottom:4.5rem; }
.ps-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.ps-card {
  border-radius:22px; padding:2.25rem 2rem;
  border:1px solid var(--border); position:relative; overflow:hidden;
}
.ps-before { background:var(--bg-2); }
.ps-after {
  background:linear-gradient(145deg,rgba(122,58,142,0.08),rgba(122,58,142,0.02));
  border-color:rgba(122,58,142,0.22);
}
.ps-after::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(122,58,142,0.5),transparent);
}
.ps-pill {
  display:inline-flex; align-items:center; gap:.4rem;
  padding:.25rem .75rem; border-radius:100px;
  font-size:.7rem; font-weight:600; margin-bottom:1.25rem;
}
.ps-pill-before { background:rgba(220,38,38,0.10); color:var(--red); }
.ps-pill-after  { background:rgba(22,163,74,0.10);  color:var(--green); }
@media (prefers-color-scheme: dark) {
  .ps-pill-before { background:rgba(239,68,68,0.15); }
  .ps-pill-after  { background:rgba(34,197,94,0.15); }
}
.ps-card h3 { font-family:var(--serif); font-size:1.35rem; letter-spacing:-.02em; margin-bottom:1.4rem; }
.ps-items { display:flex; flex-direction:column; gap:.85rem; }
.ps-item { display:flex; align-items:flex-start; gap:.8rem; }
.ps-ico {
  width:28px; height:28px; border-radius:7px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; margin-top:.05rem;
}
.ps-ico-b { background:rgba(220,38,38,0.10); color:var(--red); }
.ps-ico-a { background:rgba(22,163,74,0.10); color:var(--green); }
@media (prefers-color-scheme: dark) {
  .ps-ico-b { background:rgba(239,68,68,0.14); }
  .ps-ico-a { background:rgba(34,197,94,0.14); }
}
.ps-item-text { font-size:.875rem; color:var(--text-2); line-height:1.65; }

/* ================================================================
   FEATURES
   ================================================================ */
.features { padding:8rem 0; }
.features-header { margin-bottom:3.5rem; }
.feat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.1rem; }
.feat-card {
  background:var(--bg-2); border:1px solid var(--border);
  border-radius:18px; padding:1.75rem;
  position:relative; overflow:hidden;
  transition:border-color .25s,transform .25s,box-shadow .25s;
}
.feat-card::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:transparent; transition:background .3s;
}
.feat-card:hover { border-color:rgba(122,58,142,0.35); transform:translateY(-4px); box-shadow:var(--shadow-md), 0 0 24px rgba(122,58,142,0.10); }
.feat-card:hover::before { background:linear-gradient(90deg,transparent,rgba(122,58,142,0.45),transparent); }
.feat-icon {
  width:44px; height:44px; border-radius:11px;
  display:flex; align-items:center; justify-content:center;
  margin-bottom:1.1rem; color:var(--p2);
  transition:transform .25s;
}
@media (prefers-color-scheme: dark) { .feat-icon { color:var(--p3); } }
.feat-card:hover .feat-icon { transform:scale(1.08) rotate(4deg); }
.feat-card h3 { font-family:var(--serif); font-size:1.1rem; letter-spacing:-.02em; margin-bottom:.45rem; }
.feat-card p { font-size:.855rem; color:var(--text-2); line-height:1.72; }

.feat-card:nth-child(1) .feat-icon { background:rgba(122,58,142,0.10); border:1px solid rgba(122,58,142,0.18); }
.feat-card:nth-child(2) .feat-icon { background:rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.16); color:var(--green); }
.feat-card:nth-child(3) .feat-icon { background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.16); color:var(--blue); }
.feat-card:nth-child(4) .feat-icon { background:rgba(202,138,4,0.08); border:1px solid rgba(202,138,4,0.16); color:var(--yellow); }
.feat-card:nth-child(5) .feat-icon { background:rgba(220,38,38,0.07); border:1px solid rgba(220,38,38,0.14); color:var(--red); }
.feat-card:nth-child(6) .feat-icon { background:rgba(122,58,142,0.10); border:1px solid rgba(122,58,142,0.18); }
@media (prefers-color-scheme: dark) {
  .feat-card:nth-child(2) .feat-icon { background:rgba(34,197,94,0.10); border-color:rgba(34,197,94,0.2); }
  .feat-card:nth-child(3) .feat-icon { background:rgba(96,165,250,0.10); border-color:rgba(96,165,250,0.2); }
  .feat-card:nth-child(4) .feat-icon { background:rgba(234,179,8,0.10); border-color:rgba(234,179,8,0.2); }
  .feat-card:nth-child(5) .feat-icon { background:rgba(239,68,68,0.10); border-color:rgba(239,68,68,0.18); }
}

/* ================================================================
   FULL DASHBOARD PREVIEW
   ================================================================ */
.preview { padding:8rem 0; text-align:center; }
.preview-wrap { margin-top:3rem; position:relative; }
.preview-glow {
  position:absolute; bottom:-50px; left:50%; transform:translateX(-50%);
  width:60%; height:80px;
  background:radial-gradient(ellipse,rgba(122,58,142,0.3),transparent 70%);
  pointer-events:none;
}
.big-mock {
  background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:22px; overflow:hidden; text-align:left;
  box-shadow:var(--shadow-lg), 0 0 80px rgba(122,58,142,0.08);
  position:relative;
}
.big-mock::after {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(122,58,142,0.55),transparent);
}
.bm-topbar { padding:.7rem 1.2rem; background:var(--bg-3); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:.75rem; }
.bm-dots { display:flex; gap:5px; }
.bm-dot { width:9px; height:9px; border-radius:50%; }
.bm-dot:nth-child(1){background:#ff5f57}.bm-dot:nth-child(2){background:#febc2e}.bm-dot:nth-child(3){background:#28c840}
.bm-url { flex:1; max-width:280px; margin:0 auto; background:var(--surface); border:1px solid var(--border); border-radius:5px; height:22px; display:flex; align-items:center; justify-content:center; font-size:.65rem; color:var(--text-3); }
.bm-body { display:grid; grid-template-columns:195px 1fr; min-height:520px; }
.bm-sidebar { background:var(--bg-3); border-right:1px solid var(--border); padding:1.2rem .85rem; display:flex; flex-direction:column; gap:2px; }
.bm-logo { display:flex; align-items:center; gap:.55rem; font-family:var(--serif); font-size:.95rem; color:var(--text); padding:.25rem .5rem 1rem; }
.bm-logo-mark { width:20px; height:20px; background:var(--p2); border-radius:5px; display:flex; align-items:center; justify-content:center; }
.bm-nav { display:flex; align-items:center; gap:.55rem; padding:.48rem .65rem; border-radius:7px; font-size:.7rem; color:var(--text-3); transition:all .15s; cursor:pointer; }
.bm-nav.active { background:rgba(122,58,142,0.14); color:var(--p2); }
@media (prefers-color-scheme: dark) { .bm-nav.active { color:var(--p4); } }
.bm-nav:hover:not(.active) { background:var(--surface); color:var(--text-2); }
.bm-nav-ico { width:14px; height:14px; flex-shrink:0; opacity:.8; }
.bm-main { padding:1.6rem; }
.bm-ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.4rem; flex-wrap:wrap; gap:.75rem; }
.bm-pt { font-size:.875rem; font-weight:600; letter-spacing:-.01em; }
.bm-ps { font-size:.63rem; color:var(--text-3); margin-top:.1rem; }
.bm-add { padding:.38rem .85rem; background:var(--surface-2); border:1px solid var(--border-2); border-radius:7px; font-size:.66rem; font-weight:500; color:var(--text); display:flex; align-items:center; gap:.35rem; transition:background .15s; }
.bm-add:hover { background:var(--surface-3); }
.bm-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.8rem; margin-bottom:1.1rem; }
.bm-stat { background:var(--surface); border:1px solid var(--border); border-radius:9px; padding:.85rem .9rem; }
.bm-sl { font-size:.6rem; text-transform:uppercase; letter-spacing:.07em; color:var(--text-3); }
.bm-sv { font-size:1.45rem; font-weight:700; margin-top:.15rem; letter-spacing:-.03em; }
.bm-sc { font-size:.6rem; margin-top:.06rem; color:var(--text-3); }
.bm-filter { display:flex; gap:.65rem; margin-bottom:.9rem; flex-wrap:wrap; }
.bm-fi { flex:1; min-width:120px; background:var(--surface); border:1px solid var(--border-2); border-radius:7px; padding:.38rem .7rem; font-size:.63rem; color:var(--text-3); display:flex; align-items:center; gap:.4rem; }
.bm-fs { background:var(--surface); border:1px solid var(--border-2); border-radius:7px; padding:.38rem .7rem; font-size:.63rem; color:var(--text-2); display:flex; align-items:center; gap:.35rem; }
.bm-table { width:100%; border-collapse:collapse; font-size:.67rem; }
.bm-table thead tr { border-bottom:1px solid var(--border); }
.bm-table th { padding:.38rem .6rem; text-align:left; font-size:.58rem; font-weight:600; text-transform:uppercase; letter-spacing:.07em; color:var(--text-3); }
.bm-table th:nth-child(3),.bm-table th:nth-child(4),.bm-table th:nth-child(5) { text-align:center; }
.bm-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
.bm-table tbody tr:last-child { border-bottom:none; }
.bm-table tbody tr:hover { background:var(--surface); }
.bm-table td { padding:.5rem .6rem; color:var(--text-2); vertical-align:middle; }
.bm-table td:first-child { color:var(--text); font-weight:500; font-size:.6rem; }
.bm-table td:nth-child(3),.bm-table td:nth-child(4),.bm-table td:nth-child(5) { text-align:center; }
.bm-status { display:inline-flex; align-items:center; padding:.14rem .46rem; border-radius:4px; font-size:.56rem; font-weight:600; }
.bm-sy { background:rgba(202,138,4,0.12); color:var(--yellow); }
.bm-sb { background:rgba(37,99,235,0.12); color:var(--blue); }
.bm-sg { background:rgba(22,163,74,0.12); color:var(--green); }
@media (prefers-color-scheme: dark) {
  .bm-sy { background:rgba(234,179,8,0.15); }
  .bm-sb { background:rgba(96,165,250,0.15); }
  .bm-sg { background:rgba(34,197,94,0.15); }
}
.bm-pag { display:flex; align-items:center; justify-content:space-between; margin-top:.9rem; padding-top:.7rem; border-top:1px solid var(--border); flex-wrap:wrap; gap:.5rem; }
.bm-pi { font-size:.6rem; color:var(--text-3); }
.bm-pb { display:flex; gap:.35rem; }
.bm-btn { padding:.22rem .52rem; background:var(--surface); border:1px solid var(--border-2); border-radius:5px; font-size:.6rem; color:var(--text-2); font-family:var(--sans); transition:background .15s; }
.bm-btn.active { background:var(--p2); color:#fff; border-color:var(--p2); }

/* ================================================================
   PRICING
   ================================================================ */
.pricing { padding:8rem 0; text-align:center; }
.pricing-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.1rem; margin-top:3.5rem; align-items:start; }
.price-card {
  background:var(--bg-2); border:1px solid var(--border); border-radius:20px;
  padding:2.1rem 1.75rem; text-align:left; transition:all .25s; position:relative; overflow:hidden;
}
.price-card:hover { border-color:rgba(122,58,142,0.35); transform:translateY(-3px); box-shadow:var(--shadow-md); }
.price-card.featured {
  background:linear-gradient(145deg,rgba(122,58,142,0.10),rgba(122,58,142,0.03));
  border-color:rgba(122,58,142,0.35); box-shadow:0 0 48px rgba(122,58,142,0.14); transform:scale(1.02);
}
.price-card.featured:hover { transform:scale(1.02) translateY(-3px); }
.price-card.featured::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,var(--p2),transparent);
}
.price-badge {
  position:absolute; top:-1px; right:1.25rem;
  background:var(--p2); color:#fff; font-size:.62rem; font-weight:600;
  padding:.24rem .68rem; border-radius:0 0 7px 7px; letter-spacing:.04em; text-transform:uppercase;
}
.price-tier { font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:var(--text-3); }
.price-amount { display:flex; align-items:baseline; gap:.25rem; margin:1.1rem 0 .15rem; }
.price-currency { font-size:1.05rem; font-weight:500; color:var(--text-2); }
.price-num { font-family:var(--serif); font-size:2.6rem; letter-spacing:-.03em; color:var(--text); }
.price-num.hl { color:var(--p2); }
@media (prefers-color-scheme: dark) { .price-num.hl { color:var(--p4); } }
.price-num.sm { font-size:1.45rem; color:var(--text-2); }
.price-period { font-size:.73rem; color:var(--text-3); margin-bottom:1.35rem; }
.price-desc { font-size:.82rem; color:var(--text-3); line-height:1.6; margin-bottom:1.35rem; }
.price-feats { display:flex; flex-direction:column; gap:.55rem; margin-bottom:1.85rem; }
.price-feat { display:flex; align-items:center; gap:.5rem; font-size:.84rem; color:var(--text-2); }
.pfyes { color:var(--green); flex-shrink:0; }
.pfno  { color:var(--text-3); flex-shrink:0; }
.btn-price {
  width:100%; padding:.78rem; border-radius:var(--radius-sm);
  font-size:.855rem; font-weight:500; transition:all .2s; font-family:var(--sans);
}
.btn-price-outline { background:transparent; border:1px solid var(--border-3); color:var(--text); }
.btn-price-outline:hover { background:var(--surface-2); transform:translateY(-1px); }
.btn-price-fill { background:var(--p2); color:#fff; box-shadow:0 0 22px rgba(122,58,142,0.35); }
.btn-price-fill:hover { background:#8B45A0; box-shadow:0 0 32px rgba(122,58,142,0.5); transform:translateY(-1px); }

/* ================================================================
   CTA / CONTACT
   ================================================================ */
.cta-section { padding:7rem 0; }
.cta-box {
  background:linear-gradient(145deg,rgba(122,58,142,0.10),rgba(82,37,102,0.04));
  border:1px solid rgba(122,58,142,0.22); border-radius:28px;
  padding:4.5rem 3.5rem; text-align:center; position:relative; overflow:hidden;
}
.cta-box::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,var(--p2),rgba(173,116,195,.5),transparent);
}
.glow-l { position:absolute; top:-80px; left:10%; width:300px; height:300px; background:radial-gradient(ellipse,rgba(122,58,142,0.15),transparent 70%); pointer-events:none; }
.glow-r { position:absolute; bottom:-60px; right:10%; width:250px; height:250px; background:radial-gradient(ellipse,rgba(173,116,195,0.08),transparent 70%); pointer-events:none; }
.cta-box h2 { font-size:clamp(1.9rem,4vw,2.75rem); margin-bottom:.9rem; }
.cta-box p { color:var(--text-2); max-width:460px; margin:0 auto 2.25rem; font-size:.975rem; }
.cta-actions { display:flex; justify-content:center; gap:1rem; flex-wrap:wrap; }
.cta-form { margin-top:2.25rem; max-width:440px; margin-left:auto; margin-right:auto; text-align:left; }
.form-row2 { display:grid; grid-template-columns:1fr 1fr; gap:.8rem; }
.form-grp { margin-bottom:.8rem; }
.form-lbl { display:block; font-size:.76rem; color:var(--text-2); margin-bottom:.35rem; font-weight:450; }
.form-ctrl {
  width:100%; background:var(--bg); border:1.5px solid var(--border-2);
  border-radius:var(--radius-sm); padding:.65rem .9rem;
  color:var(--text); font-family:var(--sans); font-size:.875rem;
  outline:none; appearance:none; transition:border-color .2s,box-shadow .2s;
}
.form-ctrl:focus { border-color:var(--p2); box-shadow:0 0 0 3px rgba(122,58,142,0.12); }
.form-ctrl::placeholder { color:var(--text-3); }
textarea.form-ctrl { resize:vertical; min-height:90px; }
select.form-ctrl option { background:var(--bg-2); color:var(--text); }
.btn-submit {
  width:100%; padding:.85rem; background:var(--p2); color:#fff;
  border-radius:var(--radius-sm); font-size:.9rem; font-weight:500;
  font-family:var(--sans); cursor:pointer;
  transition:background .2s,transform .15s,box-shadow .2s;
  box-shadow:0 0 22px rgba(122,58,142,0.35);
  display:flex; align-items:center; justify-content:center; gap:.5rem;
}
.btn-submit:hover { background:#8B45A0; transform:translateY(-2px); box-shadow:0 0 32px rgba(122,58,142,0.5); }
.btn-submit.sent  { background:#15803d; box-shadow:0 0 22px rgba(21,128,61,0.3); }
.btn-submit.error { background:#b91c1c; box-shadow:0 0 22px rgba(185,28,28,0.3); }
@media (prefers-color-scheme: dark) {
  .btn-submit.sent  { background:#16a34a; }
}

/* ================================================================
   FOOTER
   ================================================================ */
footer { border-top:1px solid var(--border); padding:3rem 0; background:var(--bg-2); }
.footer-grid { display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; gap:2.5rem; margin-bottom:2.5rem; }
.footer-brand p { font-size:.82rem; color:var(--text-3); line-height:1.7; margin-top:.7rem; max-width:200px; }
.footer-col-title { font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:var(--text-3); margin-bottom:.9rem; }
.footer-links { display:flex; flex-direction:column; gap:.45rem; }
.footer-links a { font-size:.83rem; color:var(--text-3); transition:color .2s; }
.footer-links a:hover { color:var(--text-2); }
.footer-bottom { padding-top:1.5rem; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
.footer-copy { font-size:.77rem; color:var(--text-3); }
.footer-logo { display:flex; align-items:center; gap:.55rem; font-family:var(--serif); font-size:1.1rem; color:var(--text); }
.footer-logo-mark { width:21px; height:21px; background:var(--p2); border-radius:5px; display:flex; align-items:center; justify-content:center; }

/* ================================================================
   MODALS
   ================================================================ */
.modal-backdrop {
  position:fixed; inset:0; z-index:500;
  background:rgba(0,0,0,0.5);
  backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
  display:flex; align-items:center; justify-content:center; padding:1.5rem;
  opacity:0; pointer-events:none; transition:opacity .25s ease;
}
@media (prefers-color-scheme: dark) { .modal-backdrop { background:rgba(0,0,0,0.75); } }
.modal-backdrop.open { opacity:1; pointer-events:all; }
.modal {
  background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:22px; width:100%; max-width:520px; max-height:90vh;
  overflow-y:auto; position:relative;
  transform:translateY(20px) scale(0.97);
  transition:transform .3s cubic-bezier(.4,0,.2,1);
  scrollbar-width:none; box-shadow:var(--shadow-lg);
}
.modal::-webkit-scrollbar { display:none; }
.modal-backdrop.open .modal { transform:translateY(0) scale(1); }
.modal::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(122,58,142,0.5),transparent);
  border-radius:22px 22px 0 0;
}
.modal-close {
  position:absolute; top:1rem; right:1rem;
  width:30px; height:30px; background:var(--surface-2); border:1px solid var(--border-2);
  border-radius:8px; display:flex; align-items:center; justify-content:center;
  color:var(--text-3); cursor:pointer; transition:all .15s; z-index:2;
}
.modal-close:hover { background:var(--surface-3); color:var(--text); }
.modal-body { padding:1.85rem; }
.modal-title { font-family:var(--serif); font-size:1.6rem; letter-spacing:-.02em; line-height:1.15; margin-bottom:.35rem; }
.modal-title em { font-style:italic; color:var(--p2); }
@media (prefers-color-scheme: dark) { .modal-title em { color:var(--p3); } }
.modal-sub { font-size:.84rem; color:var(--text-3); margin-bottom:1.4rem; line-height:1.65; }

/* Pricing modal */
.mprice-card {
  background:var(--bg-3); border:1px solid var(--border); border-radius:14px;
  padding:1.35rem 1.4rem; margin-bottom:.8rem; position:relative; transition:all .2s;
}
.mprice-card:hover { border-color:rgba(122,58,142,0.28); transform:translateY(-2px); box-shadow:var(--shadow-sm); }
.mprice-card.featured {
  background:linear-gradient(145deg,rgba(122,58,142,0.09),rgba(122,58,142,0.02));
  border-color:rgba(122,58,142,0.32);
}
.mprice-card.featured::before {
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(122,58,142,0.5),transparent); border-radius:14px 14px 0 0;
}
.mprice-badge { position:absolute; top:-1px; right:1rem; background:var(--p2); color:#fff; font-size:.6rem; font-weight:600; padding:.2rem .58rem; border-radius:0 0 6px 6px; text-transform:uppercase; letter-spacing:.05em; }
.mprice-tier { font-size:.66rem; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:var(--text-3); }
.mprice-amount { display:flex; align-items:baseline; gap:.2rem; margin:.55rem 0 .2rem; }
.mprice-currency { font-size:.9rem; color:var(--text-2); }
.mprice-num { font-family:var(--serif); font-size:1.9rem; letter-spacing:-.03em; color:var(--text); }
.mprice-num.hl { color:var(--p2); }
@media (prefers-color-scheme: dark) { .mprice-num.hl { color:var(--p4); } }
.mprice-num.sm { font-size:1.1rem; color:var(--text-2); }
.mprice-period { font-size:.63rem; color:var(--text-3); margin-bottom:.7rem; }
.mprice-feats { display:flex; flex-direction:column; gap:.38rem; margin-bottom:1rem; }
.mprice-feat { display:flex; align-items:center; gap:.42rem; font-size:.79rem; color:var(--text-2); }
.mpfyes { color:var(--green); flex-shrink:0; }
.mpfno  { color:var(--text-3); flex-shrink:0; }

/* ================================================================
   TOAST NOTIFICATION
   ================================================================ */
.toast {
  position:fixed; bottom:1.5rem; right:1.5rem; z-index:9000;
  background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:12px; padding:.875rem 1.25rem;
  display:flex; align-items:center; gap:.75rem;
  box-shadow:var(--shadow-lg);
  transform:translateY(120%); opacity:0;
  transition:transform .35s cubic-bezier(.4,0,.2,1), opacity .35s ease;
  max-width:320px;
}
.toast.show { transform:translateY(0); opacity:1; }
.toast-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.toast-icon.success { background:rgba(22,163,74,0.12); color:var(--green); }
.toast-title { font-size:.84rem; font-weight:600; color:var(--text); }
.toast-msg { font-size:.75rem; color:var(--text-3); margin-top:.1rem; }

/* ================================================================
   RESPONSIVE
   ================================================================ */
/* 1024px */
@media (max-width:1024px) {
  .feat-grid { grid-template-columns:repeat(2,1fr); }
  .pricing-grid { grid-template-columns:1fr; max-width:420px; margin-left:auto; margin-right:auto; }
  .price-card.featured { transform:none; }
  .price-card.featured:hover { transform:translateY(-3px); }
  .footer-grid { grid-template-columns:1fr 1fr; gap:2rem; }
  .bm-body { grid-template-columns:1fr; }
  .bm-sidebar { display:none; }
  .bm-stats { grid-template-columns:repeat(2,1fr); }
  .hero-grid { gap:3rem; }
}

/* 900px */
@media (max-width:900px) {
  .hero-grid { grid-template-columns:1fr; }
  .hero-visual { display:none; }
  .hero h1 { font-size:clamp(2.4rem,7vw,3.5rem); }
}

/* 768px */
@media (max-width:768px) {
  nav { width:calc(100% - 2rem); }
  .nav-links,.nav-cta,.nav-back { display:none; }
  .nav-hamburger { display:flex; }
  .container { padding:0 1.25rem; }
  .hero { padding:7rem 0 4rem; min-height:auto; }
  .hero-actions { flex-direction:column; align-items:stretch; }
  .hero-actions .btn-primary,.hero-actions .btn-ghost { justify-content:center; }
  .hero-metrics { gap:1.5rem; }
  .ps-grid { grid-template-columns:1fr; }
  .feat-grid { grid-template-columns:1fr; max-width:440px; margin:0 auto; }
  .ps,.features,.preview,.pricing,.cta-section { padding:5.5rem 0; }
  .cta-box { padding:2.75rem 1.5rem; }
  .form-row2 { grid-template-columns:1fr; }
  .footer-grid { grid-template-columns:1fr; gap:1.5rem; }
  .footer-bottom { flex-direction:column; align-items:flex-start; gap:.65rem; }
  .bm-main { padding:1rem; }
  .bm-stats { grid-template-columns:1fr 1fr; }
  .bm-filter { flex-wrap:wrap; }
  .bm-fi { min-width:100%; }
  .mobile-menu-footer { grid-template-columns:1fr; }
  .pricing-grid { max-width:100%; }
}

/* 480px */
@media (max-width:480px) {
  .hero h1 { font-size:2rem; }
  .metric-val { font-size:1.5rem; }
  .cta-box { padding:2rem 1.1rem; }
  .cta-actions { flex-direction:column; align-items:stretch; }
  .toast { right:.75rem; left:.75rem; max-width:none; }
  .bm-table th:nth-child(5), .bm-table td:nth-child(5) { display:none; }
  .ps-card { padding:1.75rem 1.4rem; }
  .modal-body { padding:1.35rem; }
}

/* Reduce motion */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration:.01ms !important; transition-duration:.01ms !important; }
}
</style>
</head>
<body>

<!-- CURSORS -->
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- TOAST -->
<div class="toast" id="toast">
  <div class="toast-icon success" id="toast-icon">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
  </div>
  <div>
    <div class="toast-title" id="toast-title">¡Mensaje enviado!</div>
    <div class="toast-msg" id="toast-msg">Te respondemos en menos de 24 h.</div>
  </div>
</div>

<!-- LOADER -->
<div id="loader">
  <div class="loader-wordmark">
    <div class="loader-wordmark-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
    </div>
    ArrowK
  </div>
  <div class="wheel-and-hamster" role="img" aria-label="Cargando...">
    <div class="wheel"></div>
    <div class="hamster">
      <div class="hamster__body">
        <div class="hamster__head"><div class="hamster__ear"></div><div class="hamster__eye"></div><div class="hamster__nose"></div></div>
        <div class="hamster__limb hamster__limb--fr"></div>
        <div class="hamster__limb hamster__limb--fl"></div>
        <div class="hamster__limb hamster__limb--br"></div>
        <div class="hamster__limb hamster__limb--bl"></div>
        <div class="hamster__tail"></div>
      </div>
    </div>
    <div class="spoke"></div>
  </div>
  <div class="loader-progress"><div class="loader-progress-fill"></div></div>
  <div class="loader-label">Iniciando sistema</div>
</div>

<!-- AMBIENT -->
<div class="ambient">
  <div class="ambient-grid"></div>
  <div class="ambient-orb ao1"></div>
  <div class="ambient-orb ao2"></div>
  <div class="ambient-orb ao3"></div>
</div>
<div class="noise"></div>

<!-- ====================================================  NAVBAR ==================================================== -->
<nav id="navbar">
  <a href="/" class="nav-logo">
    <div class="nav-logo-mark">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
    </div>
    ArrowK
  </a>
  <ul class="nav-links">
    <li><a href="#features">Características</a></li>
    <li><a href="#preview">Plataforma</a></li>
    <li><a href="#pricing">Precios</a></li>
    <li><a href="#contact">Contacto</a></li>
  </ul>
  <div class="nav-right">
    <a href="/" class="nav-back">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      CloudLabs
    </a>
    <button class="nav-cta" onclick="openModal('modal-contact')">Solicitar demo</button>
  </div>
  <button class="nav-hamburger" id="hamburger" aria-label="Abrir menú" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobile-menu">
  <div class="mobile-menu-links">
    <a href="#features" onclick="closeMenu()">Características <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    <a href="#preview" onclick="closeMenu()">Plataforma <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    <a href="#pricing" onclick="closeMenu()">Precios <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    <a href="#contact" onclick="closeMenu()">Contacto <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
  </div>
  <div class="mobile-menu-footer">
    <button class="btn-ghost" onclick="closeMenu(); openModal('modal-pricing')">Ver planes</button>
    <button class="btn-primary" onclick="closeMenu(); openModal('modal-contact')">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      Solicitar demo
    </button>
  </div>
</div>

<!-- MODAL — CONTACT -->
<div class="modal-backdrop" id="modal-contact" onclick="handleBackdropClick(event,'modal-contact')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-contact')" aria-label="Cerrar">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="modal-body">
      <div class="eyebrow" style="margin-bottom:.65rem"><span class="eyebrow-line"></span>Demo gratuita</div>
      <h3 class="modal-title">¿Listo para ver<br>ArrowK <em>en acción?</em></h3>
      <p class="modal-sub">Completa el formulario y agendamos una demo personalizada sin costo ni compromiso.</p>
      <div class="form-row2">
        <div class="form-grp"><label class="form-lbl">Nombre *</label><input type="text" class="form-ctrl" placeholder="Tu nombre" id="f-name"></div>
        <div class="form-grp"><label class="form-lbl">Empresa</label><input type="text" class="form-ctrl" placeholder="Nombre del negocio" id="f-company"></div>
      </div>
      <div class="form-grp"><label class="form-lbl">Correo *</label><input type="email" class="form-ctrl" placeholder="tu@correo.com" id="f-email"></div>
      <div class="form-grp">
        <label class="form-lbl">Tu rol</label>
        <select class="form-ctrl" id="f-role">
          <option value="">Selecciona tu rol</option>
          <option>Distribuidor — Vendo bicicletas al público</option>
          <option>Vendedor — Abastezco a distribuidores</option>
          <option>Ambos roles</option>
          <option>Otro</option>
        </select>
      </div>
      <div class="form-grp"><label class="form-lbl">Mensaje</label><textarea class="form-ctrl" placeholder="Cuéntanos sobre tu operación..." id="f-message" style="min-height:80px"></textarea></div>
      <button class="btn-submit" id="btn-submit" onclick="handleSubmit()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Solicitar demo gratuita
      </button>
    </div>
  </div>
</div>

<!-- MODAL — PRICING -->
<div class="modal-backdrop" id="modal-pricing" onclick="handleBackdropClick(event,'modal-pricing')">
  <div class="modal" style="max-width:560px;">
    <button class="modal-close" onclick="closeModal('modal-pricing')">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="modal-body">
      <div class="eyebrow" style="margin-bottom:.65rem"><span class="eyebrow-line"></span>Precios</div>
      <h3 class="modal-title">Planes para cada <em>etapa</em></h3>
      <p class="modal-sub">Sin comisiones ocultas. Sin contratos forzosos.</p>
      <div class="mprice-card">
        <div class="mprice-tier">Starter</div>
        <div class="mprice-amount"><span class="mprice-currency">$</span><span class="mprice-num">299</span></div>
        <div class="mprice-period">por mes · MXN</div>
        <div class="mprice-feats">
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> POS básico + inventario 50 productos</div>
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Clientes (100) y garantías</div>
          <div class="mprice-feat"><svg class="mpfno" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg><span style="color:var(--text-3)"> Órdenes a vendedores</span></div>
        </div>
        <button class="btn-price btn-price-outline" onclick="switchModal('modal-pricing','modal-contact')">Comenzar gratis</button>
      </div>
      <div class="mprice-card featured">
        <div class="mprice-badge">Popular</div>
        <div class="mprice-tier">Pro</div>
        <div class="mprice-amount"><span class="mprice-currency">$</span><span class="mprice-num hl">499</span></div>
        <div class="mprice-period">por mes · MXN</div>
        <div class="mprice-feats">
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> POS completo + inventario ilimitado</div>
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Órdenes digitales + soporte prioritario</div>
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Garantías avanzadas</div>
        </div>
        <button class="btn-price btn-price-fill" onclick="switchModal('modal-pricing','modal-contact')">Empezar con Pro</button>
      </div>
      <div class="mprice-card">
        <div class="mprice-tier">Enterprise</div>
        <div class="mprice-amount"><span class="mprice-num sm">A la medida</span></div>
        <div class="mprice-period">&nbsp;</div>
        <div class="mprice-feats">
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Todo Pro + Red de distribuidores + API</div>
          <div class="mprice-feat"><svg class="mpfyes" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Onboarding dedicado + SLA garantizado</div>
        </div>
        <button class="btn-price btn-price-outline" onclick="switchModal('modal-pricing','modal-contact')">Hablar con ventas</button>
      </div>
    </div>
  </div>
</div>

<!-- ====================================================  HERO ==================================================== -->
<section class="hero" id="hero">
  <div class="container">
    <svg class="hero-ring" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg" fill="none">
      <circle cx="300" cy="300" r="290" stroke="rgba(122,58,142,1)" stroke-width=".6" stroke-dasharray="4 12"/>
      <circle cx="300" cy="300" r="220" stroke="rgba(122,58,142,1)" stroke-width=".6"/>
      <circle cx="300" cy="300" r="150" stroke="rgba(173,116,195,1)" stroke-width=".5" stroke-dasharray="2 8"/>
    </svg>
    <div class="hero-grid">
      <div>
        <div class="hero-badge">
          <span class="badge-dot"></span>
          CRM · ERP · POS para bicicletas eléctricas
        </div>
        <h1>El sistema que<br>transforma tu<br>negocio <em>eléctrico</em></h1>
        <p class="hero-sub">ArrowK centraliza pedidos, inventario, clientes y garantías. Reemplaza WhatsApp y hojas de cálculo con una plataforma diseñada para distribuidores y vendedores del sector.</p>
        <div class="hero-actions">
          <button class="btn-primary" onclick="openModal('modal-contact')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Solicitar demo gratis
          </button>
          <button class="btn-ghost" onclick="smoothScroll('#preview')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
            Ver plataforma
          </button>
        </div>
        <div class="hero-live">
          <span class="live-dot"></span>
          <span class="live-label">Pedidos procesados hoy —</span>
          <span class="live-count" id="live-counter">0</span>
        </div>
        <div class="hero-metrics">
          <div>
            <div class="metric-val" data-count="3" data-suffix="×">3×</div>
            <div class="metric-label">Más ágil que WhatsApp</div>
          </div>
          <div>
            <div class="metric-val" data-count="100" data-suffix="%">100%</div>
            <div class="metric-label">Trazabilidad</div>
          </div>
          <div>
            <div class="metric-val" data-count="0">0</div>
            <div class="metric-label">Pedidos sin seguimiento</div>
          </div>
        </div>
      </div>

      <!-- Dashboard mockup -->
      <div class="hero-visual">
        <div class="float-card float-card-a">
          <div class="fc-label">Ventas del día</div>
          <div class="fc-val green">+$12,400</div>
        </div>
        <div class="float-card float-card-b">
          <div class="fc-label">Stock disponible</div>
          <div class="fc-val purple">48 unidades</div>
        </div>
        <div class="mockup-shell">
          <div class="m-topbar">
            <div class="m-dots"><div class="m-dot"></div><div class="m-dot"></div><div class="m-dot"></div></div>
            <div class="m-url-bar">app.arrowk.io — Dashboard</div>
          </div>
          <div class="m-body">
            <div class="m-sidebar">
              <div class="m-logo">
                <div class="m-logo-mark"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
                ArrowK
              </div>
              <div class="m-nav active" data-tab="dashboard"><svg class="m-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</div>
              <div class="m-nav" data-tab="pedidos"><svg class="m-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Pedidos</div>
              <div class="m-nav" data-tab="inventario"><svg class="m-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>Inventario</div>
              <div class="m-nav" data-tab="clientes"><svg class="m-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Clientes</div>
              <div class="m-nav" data-tab="garantias"><svg class="m-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Garantías</div>
            </div>
            <div class="m-main" id="mockup-main"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TRUST STRIP -->
<div class="strip">
  <div class="strip-track" id="stripTrack">
    <span class="strip-label">Diseñado para</span>
    <span class="strip-sep"></span><span class="strip-item">Distribuidores independientes</span>
    <span class="strip-sep"></span><span class="strip-item">Redes de distribución</span>
    <span class="strip-sep"></span><span class="strip-item">Vendedores mayoristas</span>
    <span class="strip-sep"></span><span class="strip-item">Puntos de venta físicos</span>
    <span class="strip-sep"></span><span class="strip-item">E-commerce de bicicletas eléctricas</span>
    <span class="strip-sep"></span>
    <span class="strip-label">Diseñado para</span>
    <span class="strip-sep"></span><span class="strip-item">Distribuidores independientes</span>
    <span class="strip-sep"></span><span class="strip-item">Redes de distribución</span>
    <span class="strip-sep"></span><span class="strip-item">Vendedores mayoristas</span>
    <span class="strip-sep"></span><span class="strip-item">Puntos de venta físicos</span>
    <span class="strip-sep"></span><span class="strip-item">E-commerce de bicicletas eléctricas</span>
    <span class="strip-sep"></span>
  </div>
</div>

<!-- PROBLEM / SOLUTION -->
<section class="ps" id="problem">
  <div class="container">
    <div class="ps-header rv">
      <div class="eyebrow"><span class="eyebrow-line"></span>El problema</div>
      <h2>Del caos informal<br>al <em>control total</em></h2>
      <p class="sub" style="margin:.75rem auto 0">Así opera la mayoría de distribuidores hoy. Y así operan con ArrowK.</p>
    </div>
    <div class="ps-grid">
      <div class="ps-card ps-before rv rv-left">
        <div class="ps-pill ps-pill-before"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Sin ArrowK</div>
        <h3>Operación fragmentada</h3>
        <div class="ps-items">
          <div class="ps-item"><div class="ps-ico ps-ico-b"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><span class="ps-item-text">Pedidos por WhatsApp sin confirmación formal ni trazabilidad</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-b"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><span class="ps-item-text">Inventario en Excel desactualizado, propenso a errores humanos</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-b"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><span class="ps-item-text">Garantías imposibles de rastrear por falta de registros centralizados</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-b"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><span class="ps-item-text">Clientes sin historial ni seguimiento post-venta efectivo</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-b"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><span class="ps-item-text">Horas invertidas en tareas administrativas repetitivas cada semana</span></div>
        </div>
      </div>
      <div class="ps-card ps-after rv rv-right">
        <div class="ps-pill ps-pill-after"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Con ArrowK</div>
        <h3>Operación centralizada</h3>
        <div class="ps-items">
          <div class="ps-item"><div class="ps-ico ps-ico-a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><span class="ps-item-text">Órdenes digitales con aprobación, seguimiento y trazabilidad completa</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><span class="ps-item-text">Stock actualizado automáticamente en cada venta o entrada</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><span class="ps-item-text">Módulo de garantías con historial completo por número de serie</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><span class="ps-item-text">CRM completo con historial, notas y seguimiento de cada cliente</span></div>
          <div class="ps-item"><div class="ps-ico ps-ico-a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><span class="ps-item-text">Flujos comerciales automatizados entre vendedores y distribuidores</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
  <div class="container">
    <div class="features-header rv">
      <div class="eyebrow"><span class="eyebrow-line"></span>Características</div>
      <h2>Todo lo que necesitas<br>en <em>un solo sistema</em></h2>
      <p class="sub">Módulos diseñados para la cadena de valor completa del sector de bicicletas eléctricas.</p>
    </div>
    <div class="feat-grid">
      <div class="feat-card rv d1">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
        <h3>POS Inteligente</h3>
        <p>Punto de venta optimizado para bicicletas eléctricas. Registra ventas, aplica descuentos y genera tickets en segundos, con soporte offline.</p>
      </div>
      <div class="feat-card rv d2">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg></div>
        <h3>Inventario Automático</h3>
        <p>El stock se ajusta con cada operación. Alertas de mínimos, historial de movimientos y valoración de inventario en tiempo real.</p>
      </div>
      <div class="feat-card rv d3">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
        <h3>Gestión de Clientes</h3>
        <p>CRM completo con historial de compras, garantías activas y seguimiento. Cada cliente, perfectamente documentado.</p>
      </div>
      <div class="feat-card rv d1">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <h3>Control de Garantías</h3>
        <p>Registra y rastrea garantías por número de serie. Alertas de vencimiento e historial de reclamaciones centralizado.</p>
      </div>
      <div class="feat-card rv d2">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></div>
        <h3>Órdenes Digitales</h3>
        <p>Distribuidores solicitan pedidos directamente a sus vendedores con aprobación, seguimiento y trazabilidad completa.</p>
      </div>
      <div class="feat-card rv d3">
        <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
        <h3>Dashboard de Vendedor</h3>
        <p>Gestiona todos tus distribuidores, recibe pedidos y mantén visibilidad total sobre tu red comercial en un solo lugar.</p>
      </div>
    </div>
  </div>
</section>

<!-- FULL DASHBOARD PREVIEW -->
<section class="preview" id="preview">
  <div class="container">
    <div class="rv">
      <div class="eyebrow" style="justify-content:center"><span class="eyebrow-line"></span>Vista del producto</div>
      <h2>Diseñado para <em>escalar</em></h2>
      <p class="sub" style="margin:.75rem auto 0">Dashboard completo que refleja la operación real de pedidos, inventario y ventas.</p>
    </div>
    <div class="preview-wrap rv">
      <div class="big-mock">
        <div class="bm-topbar">
          <div class="bm-dots"><div class="bm-dot"></div><div class="bm-dot"></div><div class="bm-dot"></div></div>
          <div class="bm-url">app.arrowk.io/pedidos</div>
        </div>
        <div class="bm-body">
          <div class="bm-sidebar">
            <div class="bm-logo"><div class="bm-logo-mark"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>ArrowK</div>
            <div class="bm-nav"><svg class="bm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Inicio</div>
            <div class="bm-nav"><svg class="bm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>Administrador</div>
            <div class="bm-nav active"><svg class="bm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Pedidos</div>
            <div class="bm-nav"><svg class="bm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Perfil</div>
          </div>
          <div class="bm-main">
            <div class="bm-ph">
              <div><div class="bm-pt">Pedidos</div><div class="bm-ps">Gestiona los pedidos de bicicletas</div></div>
              <div class="bm-add"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Nuevo Pedido</div>
            </div>
            <div class="bm-stats">
              <div class="bm-stat"><div class="bm-sl">Total</div><div class="bm-sv">48</div><div class="bm-sc">pedidos registrados</div></div>
              <div class="bm-stat"><div class="bm-sl">Esta página</div><div class="bm-sv" style="color:var(--blue)">10</div><div class="bm-sc">pedidos visibles</div></div>
              <div class="bm-stat"><div class="bm-sl">Página</div><div class="bm-sv">1<span style="font-size:.85rem;color:var(--text-3);font-weight:400">/5</span></div><div class="bm-sc">de 5 páginas</div></div>
            </div>
            <div class="bm-filter">
              <div class="bm-fi"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>N° Pedido o Negocio</div>
              <div class="bm-fs"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Status: Todos</div>
            </div>
            <table class="bm-table">
              <thead><tr><th>N° Pedido</th><th>Negocio</th><th>Items</th><th>Status</th><th>Fecha</th></tr></thead>
              <tbody>
                <tr><td>PED-00048</td><td>Distribuidora Norte</td><td>3</td><td><span class="bm-status bm-sy">Solicitado</span></td><td>09/03/2025</td></tr>
                <tr><td>PED-00047</td><td>Bici Express</td><td>5</td><td><span class="bm-status bm-sb">Preparado</span></td><td>08/03/2025</td></tr>
                <tr><td>PED-00046</td><td>VoltageMX</td><td>2</td><td><span class="bm-status bm-sg">Entregado</span></td><td>07/03/2025</td></tr>
                <tr><td>PED-00045</td><td>EcoRide Sur</td><td>4</td><td><span class="bm-status bm-sg">Entregado</span></td><td>06/03/2025</td></tr>
                <tr><td>PED-00044</td><td>PowerCycle MX</td><td>1</td><td><span class="bm-status bm-sy">Solicitado</span></td><td>05/03/2025</td></tr>
              </tbody>
            </table>
            <div class="bm-pag">
              <span class="bm-pi">Mostrando 1–10 de 48 pedidos</span>
              <div class="bm-pb">
                <button class="bm-btn active">1</button>
                <button class="bm-btn">2</button>
                <button class="bm-btn">3</button>
                <button class="bm-btn">→</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="preview-glow"></div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="pricing" id="pricing">
  <div class="container">
    <div class="rv">
      <div class="eyebrow" style="justify-content:center"><span class="eyebrow-line"></span>Precios</div>
      <h2>Planes para cada <em>etapa</em></h2>
      <p class="sub" style="margin:.75rem auto 0">Sin comisiones ocultas. Sin contratos forzosos. Cancela en cualquier momento.</p>
    </div>
    <div class="pricing-grid">
      <div class="price-card rv d1">
        <div class="price-tier">Starter</div>
        <div class="price-amount"><span class="price-currency">$</span><span class="price-num">299</span></div>
        <div class="price-period">por mes · MXN</div>
        <p class="price-desc">Para distribuidores independientes que buscan organizarse.</p>
        <div class="price-feats">
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> POS básico</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Inventario hasta 50 productos</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Clientes (100) y garantías</div>
          <div class="price-feat"><svg class="pfno" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg><span style="color:var(--text-3)"> Órdenes a vendedores</span></div>
        </div>
        <button class="btn-price btn-price-outline" onclick="openModal('modal-contact')">Comenzar gratis</button>
      </div>
      <div class="price-card featured rv d2">
        <div class="price-badge">Popular</div>
        <div class="price-tier">Pro</div>
        <div class="price-amount"><span class="price-currency">$</span><span class="price-num hl">499</span></div>
        <div class="price-period">por mes · MXN</div>
        <p class="price-desc">Para distribuidores activos conectados a una red de vendedores.</p>
        <div class="price-feats">
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> POS completo con reportes</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Inventario y clientes ilimitados</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Órdenes digitales a vendedores</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Soporte prioritario</div>
        </div>
        <button class="btn-price btn-price-fill" onclick="openModal('modal-contact')">Empezar con Pro</button>
      </div>
      <div class="price-card rv d3">
        <div class="price-tier">Enterprise</div>
        <div class="price-amount"><span class="price-num sm">A la medida</span></div>
        <div class="price-period">&nbsp;</div>
        <p class="price-desc">Para vendedores y redes de distribuidores a gran escala.</p>
        <div class="price-feats">
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Todo lo de Pro</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Red de distribuidores + API</div>
          <div class="price-feat"><svg class="pfyes" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Onboarding dedicado + SLA</div>
        </div>
        <button class="btn-price btn-price-outline" onclick="openModal('modal-contact')">Hablar con ventas</button>
      </div>
    </div>
  </div>
</section>

<!-- CTA / CONTACT -->
<section class="cta-section" id="contact">
  <div class="container">
    <div class="cta-box rv">
      <div class="glow-l"></div>
      <div class="glow-r"></div>
      <div class="eyebrow" style="justify-content:center"><span class="eyebrow-line"></span>Hablemos</div>
      <h2>¿Listo para <em>digitalizar</em><br>tu operación?</h2>
      <p>Cuéntanos de qué se trata. Respondemos en menos de 24 horas con una demo personalizada sin costo ni compromiso.</p>
      <div class="cta-form">
        <div class="form-row2">
          <div class="form-grp"><label class="form-lbl">Nombre *</label><input type="text" class="form-ctrl" placeholder="Tu nombre" id="fc-name"></div>
          <div class="form-grp"><label class="form-lbl">Empresa</label><input type="text" class="form-ctrl" placeholder="Tu negocio" id="fc-company"></div>
        </div>
        <div class="form-grp"><label class="form-lbl">Correo *</label><input type="email" class="form-ctrl" placeholder="tu@correo.com" id="fc-email"></div>
        <div class="form-grp">
          <label class="form-lbl">¿En qué te ayudamos?</label>
          <select class="form-ctrl" id="fc-service">
            <option value="">Selecciona una opción</option>
            <option>Demo de ArrowK</option>
            <option>Plan Starter</option>
            <option>Plan Pro</option>
            <option>Plan Enterprise</option>
            <option>Otro</option>
          </select>
        </div>
        <div class="form-grp"><label class="form-lbl">Mensaje</label><textarea class="form-ctrl" placeholder="Cuéntanos sobre tu operación actual..." id="fc-message"></textarea></div>
        <button class="btn-submit" id="btn-submit-main" onclick="handleSubmitMain()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Enviar mensaje
        </button>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">
          <div class="footer-logo-mark"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
          ArrowK
        </div>
        <p>CRM + ERP + POS para distribuidores y vendedores del sector de bicicletas eléctricas.</p>
      </div>
      <div>
        <div class="footer-col-title">Producto</div>
        <div class="footer-links">
          <a href="#features">Características</a>
          <a href="#preview">Plataforma</a>
          <a href="#pricing">Precios</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Empresa</div>
        <div class="footer-links">
          <a href="/">CloudLabs</a>
          <a href="#contact">Contacto</a>
          <a href="#">Privacidad</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Contacto</div>
        <div class="footer-links">
          <a href="mailto:cloudlabs342@gmail.com">cloudlabs342@gmail.com</a>
          <a href="https://wa.me/5512416031">WhatsApp</a>
          <a href="#contact">Solicitar demo</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-logo" style="font-size:1rem;">
        <div class="footer-logo-mark" style="width:18px;height:18px;"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
        ArrowK
      </div>
      <span class="footer-copy">© 2026 ArrowK · Un producto de CloudLabs · Ciudad de México</span>
      <span class="footer-copy"><a href="/" style="color:var(--text-3);">← CloudLabs</a></span>
    </div>
  </div>
</footer>

<!-- SCRIPTS -->
<script>
/* ── CURSOR ── */
const cur = document.getElementById('cursor');
const curRing = document.getElementById('cursorRing');
if (cur && curRing) {
  let mx=0,my=0,rx=0,ry=0;
  document.addEventListener('mousemove',e=>{ mx=e.clientX;my=e.clientY;cur.style.left=mx+'px';cur.style.top=my+'px'; });
  function animRing(){ rx+=(mx-rx)*.14;ry+=(my-ry)*.14;curRing.style.left=rx+'px';curRing.style.top=ry+'px';requestAnimationFrame(animRing); }
  animRing();
}

/* ── LOADER ── */
const loaderEl = document.getElementById('loader');
let loaderDismissed = false;
function dismissLoader(){ loaderDismissed=true; loaderEl.classList.add('hidden'); }
window.addEventListener('load',()=>setTimeout(dismissLoader,2700));
window.addEventListener('pageshow',e=>{ if(e.persisted){ loaderEl.style.transition='none'; loaderEl.classList.add('hidden'); loaderDismissed=true; }});

/* ── NAVBAR ── */
const navEl = document.getElementById('navbar');
window.addEventListener('scroll',()=>navEl.classList.toggle('scrolled',window.scrollY>60),{passive:true});

/* ── MOBILE MENU ── */
function toggleMenu(){
  const m=document.getElementById('mobile-menu'),b=document.getElementById('hamburger');
  const isOpen=m.classList.contains('open');
  if(isOpen){ closeMenu(); }else{ m.classList.add('open');b.classList.add('open'); }
}
function closeMenu(){ document.getElementById('mobile-menu').classList.remove('open');document.getElementById('hamburger').classList.remove('open'); }
document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ closeMenu();closeModal('modal-contact');closeModal('modal-pricing'); }});

/* ── SMOOTH SCROLL ── */
function smoothScroll(sel){ closeMenu();setTimeout(()=>document.querySelector(sel)?.scrollIntoView({behavior:'smooth'}),50); }

/* ── MODALS ── */
function openModal(id){ closeMenu();const el=document.getElementById(id);if(!el)return;el.classList.add('open');document.body.style.overflow='hidden'; }
function closeModal(id){ const el=document.getElementById(id);if(!el)return;el.classList.remove('open');document.body.style.overflow=''; }
function handleBackdropClick(e,id){ if(e.target===e.currentTarget)closeModal(id); }
function switchModal(from,to){ closeModal(from);setTimeout(()=>openModal(to),200); }

/* ── TOAST ── */
function showToast(title,msg,type='success'){
  const t=document.getElementById('toast');
  document.getElementById('toast-title').textContent=title;
  document.getElementById('toast-msg').textContent=msg;
  document.getElementById('toast-icon').className='toast-icon '+type;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),4200);
}

/* ── SCROLL REVEAL ── */
const revObs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in');revObs.unobserve(e.target); }});
},{threshold:.07,rootMargin:'0px 0px -28px 0px'});
document.querySelectorAll('.rv,.rv-left,.rv-right').forEach(el=>revObs.observe(el));

/* ── COUNTERS ── */
function animCounter(el,target,suffix=''){
  const dur=1600,start=performance.now();
  const tick=now=>{ const p=Math.min((now-start)/dur,1),ease=1-(1-p)**3;el.textContent=Math.round(ease*target)+suffix;if(p<1)requestAnimationFrame(tick); };
  requestAnimationFrame(tick);
}
const cntObs=new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting){ const el=e.target;animCounter(el,+el.dataset.count,el.dataset.suffix||'');cntObs.unobserve(el); }});
},{threshold:.5});
document.querySelectorAll('[data-count]').forEach(el=>cntObs.observe(el));

/* ── LIVE COUNTER ── */
const liveEl=document.getElementById('live-counter');
let liveCount=Math.floor(Math.random()*80)+120;
liveEl.textContent=liveCount;
setInterval(()=>{
  if(Math.random()<.35){
    liveCount+=Math.floor(Math.random()*3)+1;
    liveEl.style.transform='scale(1.3)';
    setTimeout(()=>{ liveEl.textContent=liveCount;liveEl.style.transform='scale(1)'; },150);
  }
},2800);

/* ── DASHBOARD MOCKUP TABS ── */
const tabs = {
  dashboard:()=>`
    <div class="m-ptitle">Dashboard General</div>
    <div class="m-stats-row">
      <div class="m-stat"><div class="m-stat-l">Ventas</div><div class="m-stat-v" style="color:var(--p2)">$284k</div><div class="m-stat-c" style="color:var(--green)">+18%</div></div>
      <div class="m-stat"><div class="m-stat-l">Unidades</div><div class="m-stat-v" style="color:var(--blue)">142</div><div class="m-stat-c" style="color:var(--green)">+24</div></div>
      <div class="m-stat"><div class="m-stat-l">Stock</div><div class="m-stat-v" style="color:var(--green)">63</div><div class="m-stat-c" style="color:var(--yellow)">3 bajos</div></div>
    </div>
    <div class="m-bars-wrap"><div class="m-bars-label">Ventas por mes</div><div class="m-bars"><div class="m-bar" style="height:38%"></div><div class="m-bar" style="height:52%"></div><div class="m-bar" style="height:45%"></div><div class="m-bar" style="height:68%"></div><div class="m-bar" style="height:58%"></div><div class="m-bar" style="height:82%"></div><div class="m-bar" style="height:70%"></div><div class="m-bar" style="height:100%"></div></div></div>
    <div class="m-table-wrap"><div class="m-trow m-trow-head"><span>Producto</span><span>Estado</span></div><div class="m-trow"><span>E-Bike Pro 750W</span><span class="mbadge mbadge-g">Entregado</span></div><div class="m-trow"><span>City Rider S ×5</span><span class="mbadge mbadge-y">Pendiente</span></div><div class="m-trow"><span>Mountain E ×2</span><span class="mbadge mbadge-b">Preparado</span></div></div>`,
  pedidos:()=>`
    <div class="m-ptitle">Pedidos recientes</div>
    <div class="m-stats-row">
      <div class="m-stat"><div class="m-stat-l">Total</div><div class="m-stat-v">48</div></div>
      <div class="m-stat"><div class="m-stat-l">Pendientes</div><div class="m-stat-v" style="color:var(--yellow)">12</div></div>
      <div class="m-stat"><div class="m-stat-l">Entregados</div><div class="m-stat-v" style="color:var(--green)">36</div></div>
    </div>
    <div class="m-table-wrap"><div class="m-trow m-trow-head"><span>Pedido</span><span>Cliente</span><span>Status</span></div><div class="m-trow"><span>PED-048</span><span style="color:var(--text-3)">Dist. Norte</span><span class="mbadge mbadge-y">Solicitado</span></div><div class="m-trow"><span>PED-047</span><span style="color:var(--text-3)">Bici Express</span><span class="mbadge mbadge-b">Preparado</span></div><div class="m-trow"><span>PED-046</span><span style="color:var(--text-3)">VoltageMX</span><span class="mbadge mbadge-g">Entregado</span></div><div class="m-trow"><span>PED-045</span><span style="color:var(--text-3)">EcoRide Sur</span><span class="mbadge mbadge-g">Entregado</span></div></div>`,
  inventario:()=>`
    <div class="m-ptitle">Inventario</div>
    <div class="m-stats-row">
      <div class="m-stat"><div class="m-stat-l">Productos</div><div class="m-stat-v">24</div></div>
      <div class="m-stat"><div class="m-stat-l">Stock bajo</div><div class="m-stat-v" style="color:var(--yellow)">3</div></div>
      <div class="m-stat"><div class="m-stat-l">Valor total</div><div class="m-stat-v" style="color:var(--p2)">$1.2M</div></div>
    </div>
    <div class="m-table-wrap"><div class="m-trow m-trow-head"><span>Producto</span><span>Stock</span></div><div class="m-trow"><span>E-Bike Pro 750W</span><span style="color:var(--green)">12 uds.</span></div><div class="m-trow"><span>City Rider S</span><span style="color:var(--yellow)">3 uds.</span></div><div class="m-trow"><span>Mountain E</span><span style="color:var(--green)">8 uds.</span></div><div class="m-trow"><span>Cargo E 500W</span><span style="color:var(--red)">1 ud.</span></div></div>`,
  clientes:()=>`
    <div class="m-ptitle">Clientes</div>
    <div class="m-stats-row">
      <div class="m-stat"><div class="m-stat-l">Total</div><div class="m-stat-v">84</div></div>
      <div class="m-stat"><div class="m-stat-l">Activos</div><div class="m-stat-v" style="color:var(--green)">67</div></div>
      <div class="m-stat"><div class="m-stat-l">Nuevos</div><div class="m-stat-v" style="color:var(--blue)">+8</div></div>
    </div>
    <div class="m-table-wrap"><div class="m-trow m-trow-head"><span>Cliente</span><span>Pedidos</span></div><div class="m-trow"><span>Distribuidora Norte</span><span style="color:var(--p2)">12</span></div><div class="m-trow"><span>Bici Express</span><span style="color:var(--p2)">8</span></div><div class="m-trow"><span>VoltageMX</span><span style="color:var(--p2)">6</span></div><div class="m-trow"><span>EcoRide Sur</span><span style="color:var(--p2)">5</span></div></div>`,
  garantias:()=>`
    <div class="m-ptitle">Garantías</div>
    <div class="m-stats-row">
      <div class="m-stat"><div class="m-stat-l">Activas</div><div class="m-stat-v" style="color:var(--green)">38</div></div>
      <div class="m-stat"><div class="m-stat-l">Por vencer</div><div class="m-stat-v" style="color:var(--yellow)">4</div></div>
      <div class="m-stat"><div class="m-stat-l">Vencidas</div><div class="m-stat-v" style="color:var(--red)">2</div></div>
    </div>
    <div class="m-table-wrap"><div class="m-trow m-trow-head"><span>Serie</span><span>Producto</span><span>Estado</span></div><div class="m-trow"><span>EBK-00124</span><span style="color:var(--text-3)">E-Bike Pro</span><span class="mbadge mbadge-g">Activa</span></div><div class="m-trow"><span>EBK-00098</span><span style="color:var(--text-3)">City Rider</span><span class="mbadge mbadge-y">90 días</span></div><div class="m-trow"><span>EBK-00071</span><span style="color:var(--text-3)">Mountain E</span><span class="mbadge mbadge-g">Activa</span></div></div>`
};

const mockupMain=document.getElementById('mockup-main');
function setTab(tabId){
  document.querySelectorAll('.m-nav').forEach(n=>n.classList.remove('active'));
  document.querySelector(`.m-nav[data-tab="${tabId}"]`)?.classList.add('active');
  mockupMain.innerHTML=tabs[tabId]?tabs[tabId]():tabs.dashboard();
}
document.querySelectorAll('.m-nav').forEach(el=>{ el.addEventListener('click',()=>setTab(el.dataset.tab)); });
setTab('dashboard');

/* ── FORM SVGs ── */
const SVG={
  send:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>`,
  spin:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin_ .8s linear infinite"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>`,
  check:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
  warning:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
  error:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
};

/* ── MODAL FORM ── */
async function handleSubmit(){
  const btn=document.getElementById('btn-submit');
  const name=document.getElementById('f-name')?.value.trim()??'';
  const company=document.getElementById('f-company')?.value.trim()??'';
  const email=document.getElementById('f-email')?.value.trim()??'';
  const role=document.getElementById('f-role')?.value??'';
  const message=document.getElementById('f-message')?.value.trim()??'';
  if(!name||!email){
    btn.style.animation='shake_ .35s ease';
    btn.innerHTML=`${SVG.warning} Nombre y correo requeridos`;
    setTimeout(()=>{ btn.style.animation='';btn.innerHTML=`${SVG.send} Solicitar demo gratuita`; },2500);
    return;
  }
  btn.disabled=true; btn.innerHTML=`${SVG.spin} Enviando...`;
  try {
    const csrf=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')??'';
    const res=await fetch('/contact',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({name,company,email,role,message})});
    const data=await res.json().catch(()=>({}));
    if(res.ok){
      btn.classList.add('sent'); btn.innerHTML=`${SVG.check} Enviado — te contactamos pronto`;
      ['f-name','f-company','f-email','f-message'].forEach(id=>{ const el=document.getElementById(id);if(el)el.value=''; });
      const r=document.getElementById('f-role');if(r)r.value='';
      setTimeout(()=>closeModal('modal-contact'),2200);
      showToast('¡Demo solicitada!','Te respondemos en menos de 24 h.');
    }else{ btn.classList.add('error');btn.innerHTML=`${SVG.error} ${data.message??'Error. Intenta de nuevo.'}`; }
  }catch{ btn.classList.add('error');btn.innerHTML=`${SVG.error} Sin conexión. Revisa tu red.`; }
  btn.disabled=false;
  setTimeout(()=>{ btn.classList.remove('sent','error');btn.innerHTML=`${SVG.send} Solicitar demo gratuita`; },4000);
}

/* ── MAIN FORM ── */
async function handleSubmitMain(){
  const btn=document.getElementById('btn-submit-main');
  const name=document.getElementById('fc-name')?.value.trim()??'';
  const company=document.getElementById('fc-company')?.value.trim()??'';
  const email=document.getElementById('fc-email')?.value.trim()??'';
  const service=document.getElementById('fc-service')?.value??'';
  const message=document.getElementById('fc-message')?.value.trim()??'';
  if(!name||!email){
    btn.style.animation='shake_ .35s ease';
    btn.innerHTML=`${SVG.warning} Nombre y correo requeridos`;
    setTimeout(()=>{ btn.style.animation='';btn.innerHTML=`${SVG.send} Enviar mensaje`; },2500);
    return;
  }
  btn.disabled=true; btn.innerHTML=`${SVG.spin} Enviando...`;
  try {
    const csrf=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')??'';
    const res=await fetch('/contact',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({name,company,email,service,message})});
    const data=await res.json().catch(()=>({}));
    if(res.ok){
      btn.classList.add('sent'); btn.innerHTML=`${SVG.check} ¡Mensaje enviado!`;
      ['fc-name','fc-company','fc-email','fc-message'].forEach(id=>{ const el=document.getElementById(id);if(el)el.value=''; });
      const s=document.getElementById('fc-service');if(s)s.value='';
      showToast('¡Mensaje enviado!','Te respondemos en menos de 24 h.');
    }else{ btn.classList.add('error');btn.innerHTML=`${SVG.error} ${data.message??'Error. Intenta de nuevo.'}`; }
  }catch{ btn.classList.add('error');btn.innerHTML=`${SVG.error} Sin conexión.`; }
  btn.disabled=false;
  setTimeout(()=>{ btn.classList.remove('sent','error');btn.innerHTML=`${SVG.send} Enviar mensaje`; },4000);
}

/* ── KEYFRAMES ── */
const st=document.createElement('style');
st.textContent=`
@keyframes shake_{0%,100%{transform:translateX(0)}20%{transform:translateX(-6px)}40%{transform:translateX(6px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}
@keyframes spin_{to{transform:rotate(360deg)}}
`;
document.head.appendChild(st);
</script>
</body>
</html>