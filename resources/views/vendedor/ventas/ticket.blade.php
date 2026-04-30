"""
Servidor Python - Collar GPS ESP32
Recibe datos del ESP32 vía HTTP POST, los almacena y sirve
un dashboard web con mapa interactivo y tracking de rutas.

Instalar dependencias:
    pip install flask flask-cors

Ejecutar:
    python server.py

Acceder en el navegador:
    http://localhost:5000
"""

from flask import Flask, request, jsonify, render_template_string
from flask_cors import CORS
from datetime import datetime, timezone
import json
import threading

app = Flask(__name__)
CORS(app)

# ──────────────────────────────────────────────
#  Almacenamiento en memoria (simple, sin BD)
#  Para producción usa SQLite o InfluxDB
# ──────────────────────────────────────────────
data_lock = threading.Lock()
gps_data = {}          # { device_id: [lista de puntos] }
MAX_POINTS = 500       # Máximo puntos por dispositivo en memoria

{{-- PRODUCTOS --}}
DASHBOARD_HTML = 
<!DOCTYPE html
<html lang="es"
<head
<meta charset="UTF-8"
<meta name="viewport" content="width=device-width, initial-scale=1"
<title>Collar GPS – Dashboard</title


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script

<style
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #0f1117; color: #e2e8f0; }
  header { background: #1a1d27; padding: 14px 24px; border-bottom: 1px solid #2d3150;
           display: flex; align-items: center; gap: 16px; }
  header h1 { font-size: 18px; font-weight: 600; }
  .badge { background: #22c55e; color: #052e16; font-size: 11px; font-weight: 700;
           padding: 3px 8px; border-radius: 99px; }
  .badge.offline { background: #ef4444; color: #fff; }
  .layout { display: grid; grid-template-columns: 1fr 340px; height: calc(100vh - 53px); }
  #map { width: 100%; height: 100%; }
  .sidebar { background: #1a1d27; border-left: 1px solid #2d3150;
             display: flex; flex-direction: column; overflow: hidden; }
  .sidebar-header { padding: 14px 16px; font-size: 13px; font-weight: 600;
                    border-bottom: 1px solid #2d3150; color: #94a3b8; }
  .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 1px;
           background: #2d3150; border-bottom: 1px solid #2d3150; }
  .stat { background: #1a1d27; padding: 12px 14px; }
  .stat-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
  .stat-value { font-size: 20px; font-weight: 700; margin-top: 2px; color: #f1f5f9; }
  .stat-unit  { font-size: 11px; color: #94a3b8; }
  .points-list { flex: 1; overflow-y: auto; padding: 8px; }
  .point-row { background: #0f1117; border-radius: 8px; padding: 10px 12px;
               margin-bottom: 6px; font-size: 12px; }
  .point-row .coords { color: #60a5fa; font-weight: 600; }
  .point-row .meta   { color: #64748b; margin-top: 3px; }
  .no-signal { color: #f59e0b; font-size: 12px; text-align: center; padding: 20px; }
  .controls { padding: 12px 16px; border-top: 1px solid #2d3150; display: flex; gap: 8px; }
  button { flex: 1; padding: 8px; border-radius: 8px; border: none; cursor: pointer;
           font-size: 12px; font-weight: 600; transition: opacity .15s; }
  button:hover { opacity: .85; }
  .btn-clear  { background: #ef4444; color: #fff; }
  .btn-center { background: #3b82f6; color: #fff; }
  @media (max-width: 700px) {
    .layout { grid-template-columns: 1fr; grid-template-rows: 50vh 1fr; }
    .sidebar { border-left: none; border-top: 1px solid #2d3150; }
  }
</style>
</head>
<body>
<header>
  <h1>🐾 Collar GPS Dashboard</h1>
  <span class="badge offline" id="status-badge">Sin datos</span>
</header>

<div class="layout">
  <div id="map"></div>
  <div class="sidebar">
    <div class="sidebar-header">Últimos datos recibidos</div>
    <div class="stats">
      <div class="stat">
        <div class="stat-label">Velocidad</div>
        <div class="stat-value" id="s-speed">–</div>
        <div class="stat-unit">km/h</div>
      </div>
      <div class="stat">
        <div class="stat-label">Satélites</div>
        <div class="stat-value" id="s-sats">–</div>
        <div class="stat-unit">en vista</div>
      </div>
      <div class="stat">
        <div class="stat-label">Altitud</div>
        <div class="stat-value" id="s-alt">–</div>
        <div class="stat-unit">metros</div>
      </div>
      <div class="stat">
        <div class="stat-label">Puntos</div>
        <div class="stat-value" id="s-pts">0</div>
        <div class="stat-unit">registrados</div>
      </div>
    </div>
    <div class="points-list" id="points-list">
      <div class="no-signal">Esperando datos del collar…</div>
    </div>
    <div class="controls">
      <button class="btn-center" onclick="centerRoute()">Centrar ruta</button>
      <button class="btn-clear"  onclick="clearData()">Limpiar</button>
    </div>
  </div>
</div>

<script>
// ── Mapa Leaflet ──────────────────────────────
const map = L.map('map', { zoomControl: true }).setView([19.4326, -99.1332], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

const routeLayer = L.polyline([], {
  color: '#3b82f6', weight: 3, opacity: .85, smoothFactor: 1
}).addTo(map);

const dogIcon = L.divIcon({
  html: '<div style="font-size:26px;line-height:1;filter:drop-shadow(0 2px 4px rgba(0,0,0,.6))">🐶</div>',
  iconSize: [32, 32], iconAnchor: [16, 28], className: ''
});
let dogMarker = null;
let allPoints  = [];

// ── Polling al servidor ───────────────────────
function fetchData() {
  fetch('/api/data')
    .then(r => r.json())
    .then(data => {
      const device = Object.keys(data)[0];
      if (!device) return;
      const pts = data[device] || [];
      if (pts.length === 0) return;

      allPoints = pts;
      updateMap(pts);
      updateSidebar(pts);
    })
    .catch(() => {});
}

function updateMap(pts) {
  const validPts = pts.filter(p => p.lat && p.lon);
  if (validPts.length === 0) return;

  const latLngs = validPts.map(p => [p.lat, p.lon]);
  routeLayer.setLatLngs(latLngs);

  const last = validPts[validPts.length - 1];
  const pos  = [last.lat, last.lon];
  if (!dogMarker) {
    dogMarker = L.marker(pos, { icon: dogIcon }).addTo(map);
    map.setView(pos, 16);
  } else {
    dogMarker.setLatLng(pos);
    map.panTo(pos, { animate: true });
  }

  document.getElementById('status-badge').textContent = 'En línea';
  document.getElementById('status-badge').classList.remove('offline');
}

function updateSidebar(pts) {
  const last = pts[pts.length - 1];
  document.getElementById('s-speed').textContent = last.speed_kmh != null ? last.speed_kmh.toFixed(1) : '–';
  document.getElementById('s-sats').textContent  = last.satellites ?? '–';
  document.getElementById('s-alt').textContent   = last.altitude  != null ? last.altitude.toFixed(0) : '–';
  document.getElementById('s-pts').textContent   = pts.filter(p => p.lat).length;

  const list = document.getElementById('points-list');
  const recent = [...pts].reverse().slice(0, 40);
  list.innerHTML = recent.map(p => `
    <div class="point-row">
      ${p.lat ? `<div class="coords">${p.lat.toFixed(6)}, ${p.lon.toFixed(6)}</div>` : '<div class="coords" style="color:#f59e0b">Sin señal GPS</div>'}
      <div class="meta">${p.speed_kmh != null ? p.speed_kmh.toFixed(1)+' km/h' : ''} · ${p.satellites ?? 0} sats · ${new Date(p.server_time).toLocaleTimeString('es-MX')}</div>
    </div>`).join('');
}

function centerRoute() {
  const valid = allPoints.filter(p => p.lat && p.lon);
  if (valid.length > 0) {
    const bounds = L.latLngBounds(valid.map(p => [p.lat, p.lon]));
    map.fitBounds(bounds, { padding: [30, 30] });
  }
}

function clearData() {
  fetch('/api/clear', { method: 'POST' }).then(() => {
    allPoints = [];
    routeLayer.setLatLngs([]);
    if (dogMarker) { map.removeLayer(dogMarker); dogMarker = null; }
    document.getElementById('points-list').innerHTML = '<div class="no-signal">Datos limpiados.</div>';
    document.getElementById('status-badge').textContent = 'Sin datos';
    document.getElementById('status-badge').classList.add('offline');
  });
}

// Actualizar cada 2 segundos
setInterval(fetchData, 2000);
fetchData();
</script
</body>
</html>
"""

# ──────────────────────────────────────────────
#  RUTAS API
# ──────────────────────────────────────────────

@app.route("/")
def dashboard():
    return render_template_string(DASHBOARD_HTML)


@app.route("/api/gps", methods=["POST"])
def receive_gps():
    """Recibe JSON del ESP32 y lo almacena."""
    try:
        payload = request.get_json(force=True)
        if not payload:
            return jsonify({"error": "payload vacío"}), 400

        device_id = payload.get("device_id", "unknown")
        payload["server_time"] = datetime.now(timezone.utc).isoformat()

        with data_lock:
            if device_id not in gps_data:
                gps_data[device_id] = []
            gps_data[device_id].append(payload)
            # Limitar memoria
            if len(gps_data[device_id]) > MAX_POINTS:
                gps_data[device_id] = gps_data[device_id][-MAX_POINTS:]

        # Log en consola
        lat  = payload.get("lat", "?")
        lon  = payload.get("lon", "?")
        sats = payload.get("satellites", 0)
        spd  = payload.get("speed_kmh", 0)
        print(f"[GPS] {device_id} | lat={lat} lon={lon} | {sats} sats | {spd:.1f} km/h")

        return jsonify({"ok": True, "points": len(gps_data[device_id])}), 200

    except Exception as e:
        print(f"[ERROR] {e}")
        return jsonify({"error": str(e)}), 500


@app.route("/api/data", methods=["GET"])
def get_data():
    """Devuelve todos los puntos almacenados."""
    with data_lock:
        return jsonify(gps_data)


@app.route("/api/data/<device_id>", methods=["GET"])
def get_device_data(device_id):
    """Devuelve puntos de un dispositivo específico."""
    with data_lock:
        return jsonify(gps_data.get(device_id, []))


@app.route("/api/clear", methods=["POST"])
def clear_data():
    """Limpia todos los puntos almacenados."""
    with data_lock:
        gps_data.clear()
    return jsonify({"ok": True})


@app.route("/api/export/<device_id>", methods=["GET"])
def export_geojson(device_id):
    """Exporta la ruta como GeoJSON para usar en QGIS u otros."""
    with data_lock:
        pts = gps_data.get(device_id, [])

    valid = [p for p in pts if p.get("lat") and p.get("lon")]
    geojson = {
        "type": "FeatureCollection",
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [[p["lon"], p["lat"]] for p in valid]
                },
                "properties": {
                    "device_id": device_id,
                    "points": len(valid)
                }
            }
        ] + [
            {
                "type": "Feature",
                "geometry": {"type": "Point", "coordinates": [p["lon"], p["lat"]]},
                "properties": {k: v for k, v in p.items() if k not in ("lat", "lon")}
            }
            for p in valid
        ]
    }
    return app.response_class(
        response=json.dumps(geojson, indent=2),
        status=200,
        mimetype="application/geo+json",
        headers={"Content-Disposition": f"attachment; filename={device_id}_route.geojson"}
    )


# ──────────────────────────────────────────────
if __name__ == "__main__":
    print("=" * 50)
    print("  Collar GPS Server")
    print("  Dashboard: http://localhost:5000")
    print("  API POST:  http://localhost:5000/api/gps")
    print("  En el .ino: SERVER_URL = 'http://<tu-IP>:5000/api/gps'")
    print("=" * 50)
    app.run(host="0.0.0.0", port=5000, debug=False)