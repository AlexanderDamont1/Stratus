{{-- resources/views/admin/garantias/reclamos.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-6" x-data="reclamosAdmin()">

    {{-- Flash --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl min-w-[300px]"
             :class="flashTipo === 'error' ? 'ring-1 ring-red-200' : 'ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-5 w-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    {{-- Header + filtros --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.garantias.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                      flex items-center gap-1 mb-2 transition w-fit">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Reclamos de garantía</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $reclamos->total() }} reclamos encontrados</p>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.garantias.reclamos') }}"
              class="flex flex-wrap items-center gap-2">
            <input type="text" name="num_serie"
                   value="{{ request('num_serie') }}"
                   placeholder="Num. serie"
                   class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono w-44">

            <select name="estado"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">Todos los estados</option>
                @foreach(['pendiente','en_diagnostico','aprobado','en_reparacion','finalizado','rechazado'] as $e)
                <option value="{{ $e }}" @selected(request('estado') === $e)>
                    {{ ucfirst(str_replace('_', ' ', $e)) }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                           rounded-lg text-sm hover:opacity-90 transition active:scale-95">
                Filtrar
            </button>
        </form>
    </div>

    {{-- Tabla de reclamos --}}
    @if($reclamos->isEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    rounded-xl p-10 text-center">
            <p class="text-sm text-gray-400">No hay reclamos con los filtros seleccionados.</p>
        </div>
    @else
    <div class="space-y-3">
        @foreach($reclamos as $r)
        @php
            $sucursal    = $sucursales[$r->mantenimiento?->id_usuario ?? ''] ?? null;
            $estadoColor = match($r->estado) {
                'pendiente'      => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                'en_diagnostico' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                'aprobado'       => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'en_reparacion'  => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                'finalizado'     => 'bg-gray-100 dark:bg-gray-700 text-gray-400',
                'rechazado'      => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                default          => 'bg-gray-100 dark:bg-gray-700 text-gray-400',
            };
        @endphp

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    rounded-xl p-4 hover:shadow-md transition-all duration-200">
            <div class="flex flex-wrap items-start justify-between gap-3">

                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $r->num_serie }}
                        </span>
                        <span class="text-xs text-gray-400">·</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $r->bicicletaGarantia->garantiaDef->nombre_componente ?? $r->clave_componente }}
                        </span>
                        @if($sucursal)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30
                                     text-purple-700 dark:text-purple-300">
                            {{ $sucursal->nombre_usuario }}
                        </span>
                        @endif
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ $r->motivo_reclamo }}
                    </p>

                    @if($r->resultado)
                    <p class="text-xs text-gray-400 italic mt-1">{{ $r->resultado }}</p>
                    @endif

                    <p class="text-[10px] text-gray-400 mt-1.5">
                        {{ $r->created_at->format('d/m/Y H:i') }}
                        @if($r->mantenimiento)
                            · Mant. <span class="font-mono">{{ $r->mantenimiento->id_mantenimiento }}</span>
                        @endif
                    </p>
                </div>

                {{-- Estado + acciones --}}
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $estadoColor }}">
                        {{ ucfirst(str_replace('_', ' ', $r->estado)) }}
                    </span>

                    @if(!in_array($r->estado, ['finalizado', 'rechazado']))
                    <button @click="abrirGestion({{ json_encode([
                        'id'          => $r->id_reclamo,
                        'num_serie'   => $r->num_serie,
                        'componente'  => $r->bicicletaGarantia->garantiaDef->nombre_componente ?? $r->clave_componente,
                        'motivo'      => $r->motivo_reclamo,
                        'estado'      => $r->estado,
                        'resultado'   => $r->resultado ?? '',
                    ]) }})"
                       class="text-xs border border-gray-200 dark:border-gray-600 px-3 py-1.5
                              rounded-lg text-gray-500 dark:text-gray-400
                              hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Gestionar
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $reclamos->withQueryString()->links() }}
    </div>
    @endif

    {{-- ── MODAL: Gestionar reclamo ── --}}
    <div x-show="gestionModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
         @click.self="gestionModal = false">

        <div x-show="gestionModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>

            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1"
                x-text="reclamo?.componente"></h3>
            <p class="text-xs font-mono text-gray-400 mb-3" x-text="reclamo?.num_serie"></p>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4 text-xs text-gray-600
                        dark:text-gray-300 leading-relaxed" x-text="reclamo?.motivo"></div>

            {{-- Cambio de estado --}}
            <div class="mb-3">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                <select x-model="nuevoEstado"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                               text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_diagnostico">En diagnóstico</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="en_reparacion">En reparación</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>

            {{-- Resultado / notas --}}
            <div class="mb-4">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Resultado / observaciones
                </label>
                <textarea x-model="resultado" rows="3" placeholder="Describe el diagnóstico o resolución..."
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none">
                </textarea>
            </div>

            {{-- Reemplazo (solo si aprobado) --}}
            <div x-show="nuevoEstado === 'aprobado'" x-cloak
                 class="border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/10
                        rounded-lg p-3 mb-4">
                <p class="text-xs font-medium text-yellow-700 dark:text-yellow-400 mb-2">
                    Procesar reemplazo de componente
                </p>
                <input type="text" x-model="numSerieNuevoComp"
                       placeholder="N° serie nuevo componente (opcional)"
                       class="w-full border border-yellow-200 dark:border-yellow-700 rounded-lg px-3 py-2
                              text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                              focus:outline-none focus:ring-1 focus:ring-yellow-400">
                <p class="text-[10px] text-yellow-600 dark:text-yellow-500 mt-1.5">
                    Al procesar el reemplazo se generará una nueva garantía según la política del negocio.
                </p>
                <button @click="procesarReemplazo()"
                        :disabled="procesando"
                        class="mt-2 w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2
                               rounded-lg text-xs font-semibold transition disabled:opacity-40
                               disabled:cursor-not-allowed active:scale-95 flex items-center justify-center gap-2">
                    <svg x-show="procesando" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="procesando ? 'Procesando...' : 'Procesar reemplazo'"></span>
                </button>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="gestionModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400
                               rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button @click="guardarEstado()"
                        :disabled="guardando"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition
                               disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                    <span x-show="!guardando">Guardar estado</span>
                    <span x-show="guardando">Guardando...</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    function reclamosAdmin() {
        return {
            gestionModal:     false,
            guardando:        false,
            procesando:       false,
            reclamo:          null,
            nuevoEstado:      '',
            resultado:        '',
            numSerieNuevoComp: '',
            flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

            flash(msg, tipo = 'success') {
                this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
            },

            abrirGestion(r) {
                this.reclamo          = r;
                this.nuevoEstado      = r.estado;
                this.resultado        = r.resultado ?? '';
                this.numSerieNuevoComp = '';
                this.gestionModal     = true;
            },

            async guardarEstado() {
                this.guardando = true;
                try {
                    const res = await fetch(`/admin/garantias/reclamo/${this.reclamo.id}/estado`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify({
                            estado:    this.nuevoEstado,
                            resultado: this.resultado,
                        }),
                    });
                    const data = await res.json();
                    if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }
                    this.gestionModal = false;
                    this.flash('Estado actualizado.');
                    setTimeout(() => window.location.reload(), 1000);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.guardando = false; }
            },

            async procesarReemplazo() {
                this.procesando = true;
                try {
                    const res = await fetch(`/admin/garantias/reclamo/${this.reclamo.id}/reemplazo`, {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify({
                            num_serie_nuevo_componente: this.numSerieNuevoComp || null,
                            notas:                      this.resultado,
                        }),
                    });
                    const data = await res.json();
                    if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }
                    this.gestionModal = false;
                    this.flash(`Reemplazo procesado. Nueva garantía hasta ${data.nueva_expiracion}.`);
                    setTimeout(() => window.location.reload(), 1500);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.procesando = false; }
            },
        }
    }
    </script>
</div>
</x-app-layout>