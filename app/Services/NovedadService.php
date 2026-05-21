<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class NovedadService
{
    const JSON_PATH = 'novedades.json'; // relativo a storage/app

    // Obtener todas las novedades (array)
    public static function all(): array
    {
        if (!File::exists(storage_path(self::JSON_PATH))) {
            return [];
        }

        $content = File::get(storage_path(self::JSON_PATH));
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    // Obtener solo activas
    public static function activas(): array
    {
        return collect(self::all())
            ->filter(fn($n) => $n['activa'] ?? true)
            ->values()
            ->all();
    }

    // Crear nueva novedad
    public static function create(array $data): void
    {
        $novedades = self::all();
        $novedades[] = [
            'id'         => uniqid('nov_', true),
            'tipo'       => $data['tipo'],
            'titulo'     => $data['titulo'],
            'mensaje'    => $data['mensaje'],
            'activa'     => true,
            'created_at' => now()->toDateTimeString(),
        ];
        self::save($novedades);
    }

    // Actualizar una novedad por ID
    public static function update(string $id, array $data): void
    {
        $novedades = collect(self::all())->map(function ($n) use ($id, $data) {
            return $n['id'] === $id ? array_merge($n, $data) : $n;
        })->values()->all();
        self::save($novedades);
    }

    // Eliminar una novedad
    public static function delete(string $id): void
    {
        $novedades = collect(self::all())
            ->reject(fn($n) => $n['id'] === $id)
            ->values()
            ->all();
        self::save($novedades);
    }

    // Reordenar (si lo usas)
    public static function reorder(array $ids): void
    {
        $novedades = self::all();
        $indexed   = collect($novedades)->keyBy('id');
        $reordered = collect($ids)
            ->filter(fn($id) => isset($indexed[$id]))
            ->map(fn($id) => $indexed[$id])
            ->values()
            ->all();
        self::save($reordered);
    }

    // Guardar el array completo al archivo JSON
    private static function save(array $novedades): void
    {
        $path = storage_path(self::JSON_PATH);
        File::put($path, json_encode($novedades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}