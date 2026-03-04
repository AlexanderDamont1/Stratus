<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test WebSocket - Laravel Reverb</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        h1 {
            border-bottom: 2px solid #4299e1;
            padding-bottom: 10px;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
        }
        .status {
            padding: 10px;
            border-left: 4px solid #2196f3;
            background: #e3f2fd;
            margin-bottom: 15px;
        }
        .status.connected {
            background: #e8f5e8;
            border-left-color: #4caf50;
        }
        .message {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        input {
            padding: 8px;
            width: 60%;
        }
        button {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            background: #4299e1;
            color: white;
            cursor: pointer;
        }
        button.secondary {
            background: #48bb78;
        }
        .debug {
            background: #111827;
            color: #9ca3af;
            font-family: monospace;
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>

<h1>🔌 Test WebSocket – Laravel Reverb</h1>

<div class="container">
    <div id="connectionStatus" class="status">
        ⚡ Conectando a WebSocket...
    </div>

    <h3>📨 Mensajes recibidos</h3>
    <div id="messages" style="min-height:150px;border:1px solid #ddd;padding:10px;border-radius:4px;">
        Esperando mensajes...
    </div>

    <div style="margin-top:15px;">
        <input id="messageInput" value="Mensaje de prueba">
        <button onclick="sendTestMessage()">Enviar</button>
        <button class="secondary" onclick="clearMessages()">Limpiar</button>
    </div>

    <div class="debug" id="debugConsole">
        🐛 Debug iniciado
    </div>
</div>

<!-- LIBRERÍAS -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://unpkg.com/laravel-echo@1.16.0/dist/echo.iife.js"></script>

<script>
/* ======================================================
   CONFIGURACIÓN BASE
====================================================== */

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: '{{ env("REVERB_HOST", "192.168.100.6") }}',
    wsPort: {{ env("REVERB_PORT", 8080) }},
    forceTLS: false,
    enabledTransports: ['ws'],
    debug: true
});

const messagesDiv = document.getElementById('messages');
const statusDiv   = document.getElementById('connectionStatus');
const debugDiv    = document.getElementById('debugConsole');
const userId      = 1;

/* ======================================================
   UTILIDADES
====================================================== */

function debug(msg) {
    console.log(msg);
    debugDiv.innerHTML += `<div>[${new Date().toLocaleTimeString()}] ${msg}</div>`;
    debugDiv.scrollTop = debugDiv.scrollHeight;
}

function setStatus(connected) {
    statusDiv.textContent = connected
        ? '✅ Conectado a WebSocket'
        : '❌ Desconectado';
    statusDiv.className = connected ? 'status connected' : 'status';
}

/* ======================================================
   LISTENER (REGISTRADO SOLO CUANDO CONECTA)
====================================================== */

function registerListener() {
    debug('👂 Registrando listener en test-channel');

    Echo.leave('test-channel'); // MUY IMPORTANTE

    Echo.channel('test-channel')
        .listen('.test.message', (e) => {
            debug('📦 RAW: ' + JSON.stringify(e));

            const mensaje = e.message ?? 'Sin mensaje';
            const usuario = e.userId ?? 'Desconocido';

            const div = document.createElement('div');
            div.className = 'message';
            div.innerHTML = `
                <strong>🕐 ${new Date().toLocaleTimeString()}</strong><br>
                📌 ${mensaje} (Usuario ${usuario})
            `;

            messagesDiv.prepend(div);

            if (messagesDiv.textContent.includes('Esperando mensajes')) {
                messagesDiv.lastChild?.remove();
            }

            alert(`📨 ${mensaje}`);
        });
}

/* ======================================================
   EVENTOS DE CONEXIÓN
====================================================== */

Echo.connector.pusher.connection.bind('connected', () => {
    setStatus(true);
    debug('✅ Socket conectado');
    registerListener();
});

Echo.connector.pusher.connection.bind('disconnected', () => {
    setStatus(false);
    debug('❌ Socket desconectado');
});

/* ======================================================
   BOTONES
====================================================== */

window.sendTestMessage = function () {
    const message = document.getElementById('messageInput').value;

    debug('📤 Enviando mensaje: ' + message);

    fetch('/send-websocket-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .content
        },
        body: JSON.stringify({
            user_id: userId,
            message: message
        })
    });
};

window.clearMessages = function () {
    messagesDiv.innerHTML = 'Esperando mensajes...';
    debug('🧹 Mensajes limpiados');
};
</script>

</body>
</html>