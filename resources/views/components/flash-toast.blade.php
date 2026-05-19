
<div x-show="flashVisible" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
         :class="flashTipo === 'error'
             ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
             : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
        <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-800 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
    </div>
</div>