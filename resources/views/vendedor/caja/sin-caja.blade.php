{{-- resources/views/vendedor/caja/sin-caja.blade.php --}}
<x-app-layout>
<style>
.sc-wrap{max-width:520px;margin:5rem auto;padding:0 1.25rem;text-align:center;font-family:'Inter',system-ui,sans-serif;}
.sc-icon{font-size:3.5rem;margin-bottom:1.5rem;}
.sc-title{font-size:1.3rem;font-weight:700;margin-bottom:.6rem;letter-spacing:-.02em;}
.sc-desc{font-size:.88rem;color:#8b949e;line-height:1.65;margin-bottom:2rem;}
.sc-card{background:#161b22;border:1px solid #21262d;border-radius:10px;padding:1.5rem;text-align:left;margin-bottom:1.5rem;}
.sc-card-title{font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#8b949e;margin-bottom:.9rem;}
.sc-step{display:flex;gap:.75rem;align-items:flex-start;margin-bottom:.75rem;font-size:.83rem;line-height:1.45;color:#e6edf3;}
.sc-step:last-child{margin-bottom:0;}
.sc-step-n{min-width:22px;height:22px;border-radius:50%;background:rgba(210,153,34,.12);color:#d29922;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;margin-top:.05rem;}
.sc-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;border-radius:7px;font-weight:600;font-size:.82rem;cursor:pointer;border:1px solid rgba(210,153,34,.3);text-decoration:none;font-family:inherit;background:rgba(210,153,34,.12);color:#d29922;}
.sc-btn:hover{background:rgba(210,153,34,.2);}
</style>

<div class="sc-wrap">
    <div class="sc-icon">📭</div>
    <div class="sc-title">Caja no asignada</div>
    <div class="sc-desc">
        Tu sucursal aún no tiene una caja registradora configurada.<br>
        Solicita al administrador que te asigne una.
    </div>

    <div class="sc-card">
        <div class="sc-card-title">¿Qué debe hacer el administrador?</div>
        <div class="sc-step">
            <div class="sc-step-n">1</div>
            <div>Ir a <strong>Administración → Cajas</strong> en el menú principal.</div>
        </div>
        <div class="sc-step">
            <div class="sc-step-n">2</div>
            <div>Localizar tu sucursal (<strong>{{ auth()->user()->nombre_usuario }}</strong>) en el listado.</div>
        </div>
        <div class="sc-step">
            <div class="sc-step-n">3</div>
            <div>Hacer clic en <strong>"Asignar caja"</strong> y confirmar.</div>
        </div>
        <div class="sc-step">
            <div class="sc-step-n">4</div>
            <div>Una vez creada, podrás abrir tu primera sesión desde esta pantalla.</div>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="sc-btn">← Volver al inicio</a>
</div>
</x-app-layout>