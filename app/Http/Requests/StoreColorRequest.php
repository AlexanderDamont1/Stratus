<?php

// app/Http/Requests/StoreColorRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->id_rol, [1, 5]);
    }

    public function rules(): array
    {
        $user      = auth()->user();
        $idNegocio = $user->id_rol === 1 ? $user->id_negocio : null;
        $esRol5    = $user->id_rol === 5;

        return [
            'id_modelo' => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where('id_negocio', $idNegocio),
            ],
            'color' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($esRol5) {

                    // Rol 5 — formato simple sin hex, solo validar nombre
                    if ($esRol5) {
                        $bloqueadas = ['con', 'y', 'e', 'o', 'u', 'del', 'de', 'la', 'el', 'los', 'las'];
                        $sufijos    = ['ito', 'ita', 'itos', 'itas', 'illo', 'illa', 'ote', 'ota'];
                        $n = strtolower(trim($value));
                        if (in_array($n, $bloqueadas)) {
                            $fail("\"$n\" no es un nombre de color válido.");
                            return;
                        }
                        foreach ($sufijos as $s) {
                            if (str_ends_with($n, $s) && strlen($n) > strlen($s) + 2) {
                                $fail("\"$n\" parece un diminutivo. Usa el nombre base.");
                                return;
                            }
                        }
                        return; // ← pasa sin validar hex
                    }

                    // Rol 1 — formato con hex obligatorio: "Nombre|#hex"
                    $partes = explode('|', $value);
                    if (count($partes) !== 2) {
                        $fail('Formato inválido. Se esperaba Nombre|#hex');
                        return;
                    }
                    [$nombre, $hexRaw] = $partes;
                    $nombres = explode('/', $nombre);
                    $hexes   = explode('/', $hexRaw);

                    if (count($nombres) > 2 || count($hexes) > 2) {
                        $fail('Máximo 2 colores combinados.');
                        return;
                    }

                    $bloqueadas = ['con', 'y', 'e', 'o', 'u', 'del', 'de', 'la', 'el', 'los', 'las'];
                    $sufijos    = ['ito', 'ita', 'itos', 'itas', 'illo', 'illa', 'ote', 'ota'];

                    foreach ($nombres as $n) {
                        $n = strtolower(trim($n));
                        if (in_array($n, $bloqueadas)) {
                            $fail("\"$n\" no es un nombre de color válido.");
                            return;
                        }
                        foreach ($sufijos as $s) {
                            if (str_ends_with($n, $s) && strlen($n) > strlen($s) + 2) {
                                $fail("\"$n\" parece un diminutivo. Usa el nombre base.");
                                return;
                            }
                        }
                    }

                    foreach ($hexes as $hex) {
                        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', trim($hex))) {
                            $fail("\"$hex\" no es un hex válido.");
                            return;
                        }
                    }
                },
                Rule::unique('colores', 'color')
                    ->where('id_modelo', $this->id_modelo)
                    ->where('id_negocio', $idNegocio),
            ],
        ];
    }
}
