<x-app-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center space-y-8">

            {{-- Icono minimalista (advertencia amable) --}}
            <div class="flex justify-center">
                <div class="w-14 h-14 rounded-full border border-gray-200 dark:border-gray-700
                            flex items-center justify-center bg-transparent">
                    <svg class="w-6 h-6 text-amber-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.25"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Texto principal --}}
            <div class="space-y-2">
                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                    Suscripción finalizada
                </p>
                <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">
                    Acceso expirado
                </h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 font-normal">
                    {{ $negocio->nombre_negocio }}
                </p>
            </div>

            {{-- Tarjeta informativa --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 text-left space-y-4">
                
                <div class="flex flex-col gap-3">
                    <div class="flex gap-3 items-start">
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                            Tu suscripción venció. 
                            
                            Para renovar el servicio y seguir utilizando todas las funcionalidades,
                            contáctanos a través de WhatsApp. Podrás reactivar tu cuenta en minutos.
                        </p>
                    </div>
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-right">
                        ~ Equipo de CloudLabs
                    </p>
                </div>

                {{-- Botón de contacto (mismo estilo que en la vista original) --}}
                <div class="pt-1">
                    <a href="https://wa.me/5215511743162?text=Hola%2C%20quiero%20revovar%20mi%20subscripcion%20a%20ArrowK%2C%20{{ urlencode($negocio->id_negocio) }}"                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-gray-700 
                              hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm 
                              font-medium rounded-lg transition-all duration-150 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-whatsapp text-green-400" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>
                        <span>Renovar por WhatsApp</span>
                    </a>
                </div>
            </div>

            {{-- Cerrar sesión --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-[11px] text-gray-400 hover:text-gray-500 
                           dark:hover:text-gray-300 transition underline-offset-2">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-app-layout>