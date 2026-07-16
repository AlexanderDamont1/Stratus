function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export default function onboardingTour(steps, clave, autoIniciar = true) {
    return {
        steps: steps || [],
        clave,
        index: 0,
        activo: false,
        rect: null,
        _resizeHandler: null,
        _scrollHandler: null,

        get pasoActual() {
            return this.steps[this.index] || null;
        },

        get esUltimoPaso() {
            return this.index === this.steps.length - 1;
        },

        // Oscurece toda la pantalla y recorta un "hueco" sobre el elemento
        // del paso actual para resaltarlo. Si el paso no tiene ancla (o el
        // elemento no está visible ahora mismo), el hueco queda en 0x0 y
        // el efecto es una pantalla completamente oscura, sin resaltar nada.
        get spotlightStyle() {
            if (!this.rect) {
                const cx = window.innerWidth / 2;
                const cy = window.innerHeight / 2;
                return `top:${cy}px; left:${cx}px; width:0px; height:0px; `
                     + `box-shadow: 0 0 0 9999px rgba(0,0,0,.8);`;
            }

            const pad = 8;
            return `top:${this.rect.top - pad}px; left:${this.rect.left - pad}px; `
                 + `width:${this.rect.width + pad * 2}px; height:${this.rect.height + pad * 2}px; `
                 + `box-shadow: 0 0 0 9999px rgba(0,0,0,.8), 0 0 0 3px rgba(255,255,255,.85);`;
        },

        // Si el paso no tiene un elemento ancla (o no está visible en este
        // momento, ej. dentro de un modal cerrado), la tarjeta se centra en
        // pantalla en vez de romper el tour.
        get cardStyle() {
            if (!this.rect) {
                return 'top:50%; left:50%; transform: translate(-50%, -50%);';
            }

            const placement = this.pasoActual?.placement || 'bottom';
            const margin    = 16;
            const cardAncho = Math.min(384, window.innerWidth - 32);
            const vw = window.innerWidth;
            const vh = window.innerHeight;

            let top, left, transform = '';

            if (placement === 'top') {
                top  = this.rect.top - margin;
                left = this.rect.left;
                transform = 'transform: translateY(-100%);';
            } else if (placement === 'right') {
                top  = this.rect.top;
                left = this.rect.left + this.rect.width + margin;
            } else if (placement === 'left') {
                top  = this.rect.top;
                left = this.rect.left - margin - cardAncho;
            } else {
                top  = this.rect.top + this.rect.height + margin;
                left = this.rect.left;
            }

            left = Math.max(16, Math.min(left, vw - cardAncho - 16));
            top  = placement === 'top' ? Math.max(16, top) : Math.max(16, Math.min(top, vh - 16));

            return `top:${top}px; left:${left}px; ${transform}`;
        },

        init() {
            if (!this.steps.length) return;
            if (autoIniciar) this._activar();
        },

        // Para tours disparados por una acción del usuario (ej. abrir un
        // modal) en vez de mostrarse solos al cargar la página.
        iniciar() {
            if (!this.steps.length) return;
            this.index = 0;
            this._activar();
        },

        _activar() {
            this.activo = true;
            this._resizeHandler = () => this.actualizarPosicion();
            this._scrollHandler = () => this.actualizarPosicion();
            window.addEventListener('resize', this._resizeHandler);
            window.addEventListener('scroll', this._scrollHandler, true);
            this._irAPaso(this.index);
        },

        _irAPaso(i) {
            this.index = i;
            const paso = this.steps[i];

            if (paso?.selector) {
                window.dispatchEvent(new CustomEvent('onboarding-paso', { detail: { selector: paso.selector } }));
            }

            const el = paso?.selector ? document.querySelector(paso.selector) : null;

            if (el) {
                el.scrollIntoView({ block: 'center', behavior: 'smooth' });
                setTimeout(() => this.actualizarPosicion(), 300);
            } else {
                this.rect = null;
            }
        },

        actualizarPosicion() {
            const paso = this.pasoActual;
            const el   = paso?.selector ? document.querySelector(paso.selector) : null;
            if (!el) { this.rect = null; return; }

            const r = el.getBoundingClientRect();
            if (r.width === 0 && r.height === 0) { this.rect = null; return; }
            this.rect = { top: r.top, left: r.left, width: r.width, height: r.height };
        },

        siguiente() {
            if (this.esUltimoPaso) {
                this.finalizar();
                return;
            }
            this._irAPaso(this.index + 1);
        },

        anterior() {
            if (this.index > 0) this._irAPaso(this.index - 1);
        },

        finalizar() {
            this._terminar();
        },

        omitir() {
            this._terminar();
        },

        _terminar() {
            this.activo = false;
            window.removeEventListener('resize', this._resizeHandler);
            window.removeEventListener('scroll', this._scrollHandler, true);
            this._marcarVistoEnServidor();
        },

        async _marcarVistoEnServidor() {
            try {
                await fetch('/onboarding/completar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: JSON.stringify({ clave: this.clave }),
                });
            } catch (e) {
                console.error('No se pudo guardar el avance del onboarding:', e);
            }
        },
    };
}
