import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function csrf() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

async function fetchJson(url, method, body) {
    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-TOKEN': csrf(),
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    const data = await res.json();
    if (!res.ok) throw data;
    return data;
}

async function recargarMarcaCard(idMarca) {
    try {
        const res = await fetch(`/admin/catalogo/marca-card/${idMarca}`, {
            headers: { 'X-CSRF-TOKEN': csrf() },
        });
        if (!res.ok) return;
        const html = await res.text();

        const card = document.getElementById(`marca-card-${idMarca}`);
        if (!card) {
            window.location.reload();
            return;
        }

        const abierto = card._x_dataStack?.[0]?.abierto ?? false;

        const tmp = document.createElement('div');
        tmp.innerHTML = html.trim();
        const nuevoCard = tmp.firstElementChild;
        card.replaceWith(nuevoCard);

        Alpine.initTree(nuevoCard);

        if (abierto) {
            nuevoCard.dispatchEvent(new CustomEvent('abrir-acordeon', { bubbles: false }));
        }
    } catch (e) {
        console.error('Error recargando marca card:', e);
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('catalogoPage', () => ({

        marcaModal:   false,
        modeloModal:  false,
        colorModal:   false,
        voltajeModal: false,

        editMarcaModal:  false,
        editModeloModal: false,
        editColorModal:  false,

        deleteMarcaModal:   false,
        deleteModeloModal:  false,
        deleteColorModal:   false,
        deleteVoltajeModal: false,

        marcaActual:  null,
        modeloActual: null,
        deleteTarget: { id: null, nombre: null, action: null },

        editMarcaData:  { id: null, nombre: '' },
        editModeloData: { id: null, nombre: '', idMarca: null },
        editColorData: {
            id: null, nombre1: '', hex1: '#d5d5d5',
            nombre2: '', hex2: '#a12491',
            combinado: false, error: '', sugiriendo1: false, sugiriendo2: false,
        },

        colorNombre1: '', colorHex1: '#d5d5d5',
        colorNombre2: '', colorHex2: '#a12491',
        colorCombinado: false, colorError: '',
        sugirendoHex1: false, sugirendoHex2: false,

        flashMsg:  '',
        flashTipo: 'success',
        flashVisible: false,

        submitting: false,

        init() {
            window._catalogoPage = this;
            this._initWebSocket();
        },

        _initWebSocket() {
            const idNegocio = document.querySelector('[data-negocio-id]')?.dataset.negocioId;
            if (!idNegocio || !window.Echo) return;

            window.Echo.private(`catalogo.${idNegocio}`)
                .listen('.catalogo.actualizado', (e) => {
                    if (e.tipo === 'marca' && e.accion === 'eliminado') {
                        window.location.reload();
                        return;
                    }
                    recargarMarcaCard(e.id_marca);
                });
        },

        mostrarFlash(msg, tipo = 'success') {
            this.flashMsg     = msg;
            this.flashTipo    = tipo;
            this.flashVisible = true;
            setTimeout(() => this.flashVisible = false, 3000);
        },

        get colorValorFinal() {
            const n1 = this.colorNombre1.trim();
            const n2 = this.colorNombre2.trim();
            if (this.colorCombinado && n2)
                return `${n1}/${n2}|${this.colorHex1}/${this.colorHex2}`;
            return `${n1}|${this.colorHex1}`;
        },

        get editColorValorFinal() {
            const n1 = this.editColorData.nombre1.trim();
            const n2 = this.editColorData.nombre2.trim();
            if (this.editColorData.combinado && n2)
                return `${n1}/${n2}|${this.editColorData.hex1}/${this.editColorData.hex2}`;
            return `${n1}|${this.editColorData.hex1}`;
        },

        async submitMarca(nombre) {
            this.submitting = true;
            try {
                await fetchJson('/admin/catalogo/marcas', 'POST', { nombre_marca: nombre });
                this.marcaModal = false;
                this.mostrarFlash('Marca creada correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.nombre_marca?.[0] ?? 'Error al crear marca', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitEditMarca() {
            const { id, nombre } = this.editMarcaData;
            this.submitting = true;
            try {
                await fetchJson(`/admin/catalogo/marcas/${id}`, 'PUT', { nombre_marca: nombre });
                this.editMarcaModal = false;
                this.mostrarFlash('Marca actualizada correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.nombre_marca?.[0] ?? 'Error al actualizar', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitModelo(nombre, idMarca) {
            this.submitting = true;
            try {
                await fetchJson('/admin/catalogo/modelos', 'POST', {
                    nombre_modelo: nombre,
                    id_marca:      idMarca,
                });
                this.modeloModal = false;
                this.mostrarFlash('Modelo creado correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.nombre_modelo?.[0] ?? 'Error al crear modelo', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitEditModelo() {
            const { id, nombre, idMarca } = this.editModeloData;
            this.submitting = true;
            try {
                await fetchJson(`/admin/catalogo/modelos/${id}`, 'PUT', {
                    nombre_modelo: nombre,
                    id_marca:      idMarca,
                });
                this.editModeloModal = false;
                this.mostrarFlash('Modelo actualizado correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.nombre_modelo?.[0] ?? 'Error al actualizar', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitColor(idModelo) {
            if (!this.colorNombre1.trim() || this.colorError) return;
            this.submitting = true;
            try {
                await fetchJson('/admin/catalogo/colores', 'POST', {
                    id_modelo: idModelo ?? this.modeloActual?.id,
                    color:     this.colorValorFinal,
                });
                this.colorModal = false;
                this.mostrarFlash('Color agregado correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.color?.[0] ?? 'Error al agregar color', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitEditColor() {
            const { id, idModelo } = this.editColorData;
            if (!this.editColorData.nombre1.trim()) return;
            this.submitting = true;
            try {
                await fetchJson(`/admin/catalogo/colores/${id}`, 'PUT', {
                    id_modelo: idModelo,
                    color:     this.editColorValorFinal,
                });
                this.editColorModal = false;
                this.mostrarFlash('Color actualizado correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.color?.[0] ?? 'Error al actualizar color', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitVoltaje(idModelo, idVoltaje) {
            if (!idVoltaje) return;
            this.submitting = true;
            try {
                await fetchJson('/admin/catalogo/modelo-voltaje', 'POST', {
                    id_modelo:  idModelo,
                    id_voltaje: idVoltaje,
                });
                this.voltajeModal = false;
                this.mostrarFlash('Voltaje asignado correctamente.');
            } catch (e) {
                this.mostrarFlash(e?.errors?.id_voltaje?.[0] ?? 'Error al asignar voltaje', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitDelete() {
            const { tipo, id, action } = this.deleteTarget;
            this.submitting = true;
            try {
                await fetchJson(action, 'DELETE');
                this.deleteMarcaModal = this.deleteModeloModal =
                this.deleteColorModal = this.deleteVoltajeModal = false;
                this.mostrarFlash('Eliminado correctamente.');
            } catch (e) {
                this.deleteMarcaModal = this.deleteModeloModal =
                this.deleteColorModal = this.deleteVoltajeModal = false;
                this.mostrarFlash(e?.message ?? 'No se pudo eliminar', 'error');
            } finally {
                this.submitting = false;
            }
        },

        abrirColorModal(marcaId, modeloId, modeloNombre, marcaNombre) {
            this.marcaActual    = { id: marcaId,  nombre: marcaNombre };
            this.modeloActual   = { id: modeloId, nombre: modeloNombre };
            this.colorNombre1   = '';
            this.colorHex1      = '#d5d5d5';
            this.colorNombre2   = '';
            this.colorHex2      = '#a12491';
            this.colorCombinado = false;
            this.colorError     = '';
            this.colorModal     = true;
        },

        abrirVoltajeModal(marcaId, modeloId, modeloNombre, marcaNombre) {
            this.marcaActual  = { id: marcaId,  nombre: marcaNombre };
            this.modeloActual = { id: modeloId, nombre: modeloNombre };
            this.voltajeModal = true;
        },

        abrirModeloModal(marcaId, marcaNombre) {
            this.marcaActual = { id: marcaId, nombre: marcaNombre };
            this.modeloModal = true;
        },

        abrirEditMarca(id, nombre) {
            this.editMarcaData  = { id, nombre };
            this.editMarcaModal = true;
        },

        abrirEditModelo(id, nombre, idMarca) {
            this.editModeloData  = { id, nombre, idMarca };
            this.editModeloModal = true;
        },

        abrirEditColor(id, rawColor, idModelo) {
            const partes  = rawColor.split('|');
            const nombres = (partes[0] ?? '').split('/');
            const hexes   = (partes[1] ?? '#d5d5d5').split('/');
            this.editColorData = {
                id,
                idModelo,
                nombre1:     nombres[0] ?? '',
                hex1:        hexes[0]   ?? '#d5d5d5',
                nombre2:     nombres[1] ?? '',
                hex2:        hexes[1]   ?? '#a12491',
                combinado:   nombres.length > 1,
                error:       '',
                sugiriendo1: false,
                sugiriendo2: false,
            };
            this.editColorModal = true;
        },

        abrirDeleteModal(tipo, id, nombre, action) {
            this.deleteTarget = { tipo, id, nombre, action };
            if (tipo === 'marca')   this.deleteMarcaModal   = true;
            if (tipo === 'modelo')  this.deleteModeloModal  = true;
            if (tipo === 'color')   this.deleteColorModal   = true;
            if (tipo === 'voltaje') this.deleteVoltajeModal = true;
        },

        validarNombre(val, esEdit = false) {
            const bloqueadas = ['con','y','e','o','u','del','de','la','el','los','las'];
            const sufijos    = ['ito','ita','itos','itas','illa','ote','ota'];
            const w = val.trim().toLowerCase();
            const setError = (msg) => {
                if (esEdit) this.editColorData.error = msg;
                else        this.colorError = msg;
            };
            if (bloqueadas.includes(w)) { setError(`"${w}" no es un color válido`); return false; }
            for (const s of sufijos) {
                if (w.endsWith(s) && w.length > s.length + 2) {
                    setError(`"${w}" parece un diminutivo`); return false;
                }
            }
            setError('');
            return true;
        },

        async sugerirHex(nombre, campo) {
            if (!nombre || nombre.trim().length < 2) return;
            if (!this.validarNombre(nombre)) return;
            if (campo === 1) this.sugirendoHex1 = true;
            if (campo === 2) this.sugirendoHex2 = true;
            try {
                const res  = await fetch('/admin/catalogo/sugerir-hex', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({ nombre: nombre.trim() }),
                });
                const data = await res.json();
                if (data.hex) {
                    if (campo === 1) this.colorHex1 = data.hex;
                    if (campo === 2) this.colorHex2 = data.hex;
                }
            } catch (e) { console.error(e); }
            finally {
                if (campo === 1) this.sugirendoHex1 = false;
                if (campo === 2) this.sugirendoHex2 = false;
            }
        },

        onNombre1(val) {
            if (!this.validarNombre(val)) return;
            clearTimeout(this._timer1);
            this._timer1 = setTimeout(() => this.sugerirHex(val, 1), 600);
        },
        onNombre2(val) {
            if (!this.validarNombre(val)) return;
            clearTimeout(this._timer2);
            this._timer2 = setTimeout(() => this.sugerirHex(val, 2), 600);
        },

        async sugerirHexEdit(nombre, campo) {
            if (!nombre || nombre.trim().length < 2) return;
            if (!this.validarNombre(nombre, true)) return;
            if (campo === 1) this.editColorData.sugiriendo1 = true;
            if (campo === 2) this.editColorData.sugiriendo2 = true;
            try {
                const res  = await fetch('/admin/catalogo/sugerir-hex', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({ nombre: nombre.trim() }),
                });
                const data = await res.json();
                if (data.hex) {
                    if (campo === 1) this.editColorData.hex1 = data.hex;
                    if (campo === 2) this.editColorData.hex2 = data.hex;
                }
            } catch (e) { console.error(e); }
            finally {
                if (campo === 1) this.editColorData.sugiriendo1 = false;
                if (campo === 2) this.editColorData.sugiriendo2 = false;
            }
        },

        onEditNombre1(val) {
            if (!this.validarNombre(val, true)) return;
            clearTimeout(this._editTimer1);
            this._editTimer1 = setTimeout(() => this.sugerirHexEdit(val, 1), 600);
        },
        onEditNombre2(val) {
            if (!this.validarNombre(val, true)) return;
            clearTimeout(this._editTimer2);
            this._editTimer2 = setTimeout(() => this.sugerirHexEdit(val, 2), 600);
        },
    }));
});

Alpine.start();