<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuditAccessMiddleware
 * Registra accesos a rutas protegidas y detecta anomalías.
 * Aplicar al grupo 'auth' en bootstrap/app.php
 */
class AuditAccessMiddleware
{
    private array $skip = [
        'sanctum/csrf-cookie',
        'health',
        '_debugbar',
        'livewire/update',
        'livewire/message',
        'broadcasting/auth',
        'up',
        'favicon.ico',
        'reverb/*',

        // Audit viewer
        'root/audit-log',
        'logs/stream',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->skip as $prefix) {
            if ($request->is($prefix . '*')) {
                return $next($request);
            }
        }

        if ($request->ajax() || $request->expectsJson()) {
            return $next($request);
        }

        $response = $next($request);

        if (!$request->user()) {
            return $response;
        }

        $status = $response->getStatusCode();
        $path   = $request->path();
        $method = $request->method();

        // Acceso denegado
        if ($status === 403) {
            AuditLogger::write(AuditLogger::CAT_SECURITY, 'ACCESS_DENIED_403',
                "Acceso denegado (403) a [{$method}] /{$path}", AuditLogger::WARNING);
            return $response;
        }

        if ($status === 401) {
            AuditLogger::write(AuditLogger::CAT_SECURITY, 'ACCESS_DENIED_401',
                "No autenticado (401) intentando [{$method}] /{$path}", AuditLogger::WARNING);
            return $response;
        }

        // 404: posible enumeración
        if ($status === 404) {
            AuditLogger::write(AuditLogger::CAT_SECURITY, 'NOT_FOUND_404',
                "Recurso no encontrado: [{$method}] /{$path}", AuditLogger::INFO);
            return $response;
        }

        // Detección cross-tenant
        $user = $request->user();
        if ($user && isset($user->id_negocio)) {
            $routeNegocio = $request->route('id_negocio')
                ?? $request->route('negocio_id')
                ?? $request->input('id_negocio')
                ?? $request->input('negocio_id');

            if ($routeNegocio !== null && (int) $routeNegocio !== (int) $user->id_negocio) {
                if ((int) $user->id_rol !== 0) {
                    AuditLogger::unauthorizedCrossTenant((int) $routeNegocio, $path);
                }
            }
        }

        if ($method === 'GET' && $status < 400) {
            AuditLogger::panelAccess("[{$method}] /{$path} → HTTP {$status}");
        }

        // Registrar mutaciones
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            AuditLogger::write(
                AuditLogger::CAT_DATA,
                'HTTP_MUTATION',
                "Mutación HTTP [{$method}] /{$path} → {$status}"
            );
        }

        return $response;
    }
}