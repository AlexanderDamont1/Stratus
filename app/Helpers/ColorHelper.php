<?php

if (!function_exists('colorNombre')) {
    function colorNombre(?string $raw): string
    {
        if (!$raw) return '';
        return explode('|', $raw)[0];
    }
}

if (!function_exists('colorHexes')) {
    function colorHexes(?string $raw): array
    {
        if (!$raw) return ['#cccccc'];
        $partes = explode('|', $raw);
        $hex    = $partes[1] ?? '#cccccc';
        return explode('/', $hex);
    }
}

if (!function_exists('colorEsCombinado')) {
    function colorEsCombinado(?string $raw): bool
    {
        return str_contains(colorNombre($raw), '/');
    }
}