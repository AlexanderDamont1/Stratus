<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;


class AuditLogger
{
    // ── Niveles ────────────────────────────────────────────────────────────────
    const INFO     = 'INFO';
    const WARNING  = 'WARNING';
    const CRITICAL = 'CRITICAL';
    const DANGER   = 'DANGER';

    // ── Categorías ─────────────────────────────────────────────────────────────
    const CAT_AUTH     = 'AUTH';
    const CAT_ACCESS   = 'ACCESS';
    const CAT_DATA     = 'DATA';
    const CAT_ADMIN    = 'ADMIN';
    const CAT_SECURITY = 'SECURITY';
    const CAT_EXPORT   = 'EXPORT';
    const CAT_CONFIG   = 'CONFIG';
    const CAT_BILLING  = 'BILLING';

    // ──────────────────────────────────────────────────────────────────────────
    //  MÉTODO PRINCIPAL
    // ──────────────────────────────────────────────────────────────────────────

    public static function write(
        string $category,
        string $event,
        string $detail,
        string $level     = self::INFO,
        array  $extra     = [],
        ?string   $negocioId = null,
        ?string   $userId    = null
    ): void {

        $user     = Auth::user();
        $uId = $userId ?: ($user?->id_usuario ?: 'GUEST');
        $nId = $negocioId ?: ($user?->id_negocio ?: 'SYS');
        $rol = $user?->id_rol ?? '-';
        $method   = Request::method();
        $path     = Request::path();
        $ip       = Request::ip();
        $ts       = now()->format('Y-m-d H:i:s');

        $extraStr = '';
        if (!empty($extra)) {
            $pairs = [];
            foreach ($extra as $k => $v) {
                $pairs[] = "{$k}=" . (is_array($v) ? json_encode($v) : $v);
            }
            $extraStr = ' | ' . implode(' | ', $pairs);
        }

        $line = sprintf(
            "[%s] [%-8s] [%-8s] [NEGOCIO:%s] [USR:%s|%s] [IP:%s] [%s %s] [%s::%s] %s%s\n",
            $ts,
            $level,
            $category,
            $nId,
            $uId,
            self::rolLabel($rol),
            $ip,
            $method,
            $path,
            $category,
            $event,
            $detail,
            $extraStr
        );



        self::writeLine($line);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  HELPERS SEMÁNTICOS
    // ──────────────────────────────────────────────────────────────────────────

    public static function login(string $userId, string $negocioId, string $email): void
    {
        self::write(self::CAT_AUTH, 'LOGIN', "Inicio de sesión: {$email}",
            self::INFO, ['email' => $email], $negocioId, $userId);
    }

    public static function logout(?string $userId = null, ?string $negocioId = null): void
    {
        self::write(
            self::CAT_AUTH,
            'LOGOUT',
            "Cierre de sesión",
            self::INFO,
            [],
            $negocioId,
            $userId
        );
    }

    public static function loginFailed(string $email, string $reason = 'Credenciales incorrectas'): void
    {
        self::write(self::CAT_SECURITY, 'LOGIN_FAILED',
            "Intento fallido para: {$email} — {$reason}",
            self::WARNING, ['email' => $email, 'reason' => $reason]);
    }

    public static function loginThrottled(string $email): void
    {
        self::write(self::CAT_SECURITY, 'LOGIN_THROTTLED',
            "Rate-limit alcanzado para: {$email}. Posible fuerza bruta.",
            self::CRITICAL, ['email' => $email]);
    }

    public static function panelAccess(string $panel): void
    {
        self::write(self::CAT_ACCESS, 'PANEL_ACCESS', "Acceso a: {$panel}");
    }

    public static function unauthorizedCrossTenant(string $targetNegocioId, string $resource): void
    {
        self::write(self::CAT_SECURITY, 'CROSS_TENANT',
            "Intento de acceso a recurso de otro negocio (negocio:{$targetNegocioId}) — {$resource}",
            self::DANGER, ['target_negocio' => $targetNegocioId, 'resource' => $resource]);
    }

    public static function unauthorizedRole(string $routeRequired, string $userRol): void
    {
        self::write(self::CAT_SECURITY, 'UNAUTHORIZED_ROLE',
            "Acceso denegado — Ruta requiere rol superior al actual (rol:{$userRol})",
            self::WARNING, ['required_route' => $routeRequired, 'user_rol' => $userRol]);
    }

    public static function created(string $model, string $recordId, array $data = []): void
    {
        self::write(self::CAT_DATA, 'CREATED', "Creó {$model} #{$recordId}", self::INFO, $data);
    }

    public static function updated(string $model, string $recordId, array $changes = []): void
    {
        self::write(self::CAT_DATA, 'UPDATED', "Modificó {$model} #{$recordId}", self::INFO, $changes);
    }

    public static function deleted(string $model, string $recordId): void
    {
        self::write(self::CAT_DATA, 'DELETED', "Eliminó {$model} #{$recordId}", self::WARNING);
    }

    public static function export(string $type, string $totalRecords, string $filters = ''): void
    {
        self::write(self::CAT_EXPORT, 'EXPORT',
            "Exportó {$totalRecords} registros en formato {$type}",
            self::WARNING, ['format' => $type, 'total' => $totalRecords, 'filters' => $filters]);
    }

    public static function priceChanged(string $productId, float $oldPrice, float $newPrice): void
    {
        self::write(self::CAT_DATA, 'PRICE_CHANGED',
            "Cambio de precio en producto #{$productId}: {$oldPrice} → {$newPrice}",
            self::WARNING, ['product_id' => $productId, 'old' => $oldPrice, 'new' => $newPrice]);
    }

    public static function cashMovement(string $type, float $amount, string $sucursalId): void
    {
        self::write(self::CAT_BILLING, 'CASH_MOVEMENT',
            "Movimiento de caja [{$type}] por \${$amount} en sucursal #{$sucursalId}",
            self::INFO, ['type' => $type, 'amount' => $amount, 'sucursal' => $sucursalId]);
    }

    public static function configChanged(string $key, $oldVal, $newVal): void
    {
        self::write(self::CAT_CONFIG, 'CONFIG_CHANGED',
            "Cambió configuración [{$key}]: '{$oldVal}' → '{$newVal}'",
            self::WARNING, ['key' => $key, 'old' => $oldVal, 'new' => $newVal]);
    }

    public static function rootAction(string $action, string $targetNegocioId, string $detail = ''): void
    {
        self::write(self::CAT_ADMIN, 'ROOT_ACTION',
            "ROOT ejecutó [{$action}] sobre negocio #{$targetNegocioId}. {$detail}",
            self::CRITICAL, ['action' => $action, 'target_negocio' => $targetNegocioId]);
    }

    public static function tokenEvent(string $event, string $tokenName): void
    {
        self::write(self::CAT_SECURITY, "TOKEN_{$event}",
            "Token API [{$tokenName}] fue {$event}", self::WARNING, ['token' => $tokenName]);
    }

    public static function passwordChanged(bool $selfChange = true, ?string $targetUserId = null): void
    {
        $who = $selfChange ? 'Cambio propio' : "Forzado sobre USR:{$targetUserId}";
        self::write(self::CAT_SECURITY, 'PASSWORD_CHANGED',
            "Contraseña modificada — {$who}",
            $selfChange ? self::INFO : self::WARNING,
            ['target_user' => $targetUserId ?? 'self']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  ESCRITURA EN DISCO + BROADCAST A REVERB
    // ──────────────────────────────────────────────────────────────────────────

    private static function writeLine(string $line): void
    {
        // 1. Escribir en disco
        $dir  = storage_path('logs/audit');
        $file = $dir . '/audit-' . now()->format('Y-m-d') . '.log';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

        // 2. Emitir a Reverb (ShouldBroadcastNow = sin queue, inmediato)
        try {
            \App\Events\AuditLineWritten::dispatch(rtrim($line));
        } catch (\Throwable) {
            // Silencioso: el log en disco ya está guardado
        }
    }

    private static function rolLabel(mixed $rol): string
    {
        return match ((string) $rol) {
            0       => 'ROOT',
            1       => 'ADMIN',
            2       => 'SUCURSAL',
            default => "ROL{$rol}",
        };
    }
}