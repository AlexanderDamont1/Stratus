<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'email'   => 'required|email|max:150',
            'role'    => 'nullable|string|max:100',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            // Correo interno: notificación para el equipo ArrowK
            Mail::to('alex2204sc@gmail.com')
                ->send(new ContactMail($validated, 'internal'));

            // Correo de confirmación para el usuario
            Mail::to($validated['email'])
                ->send(new ContactMail($validated, 'confirmation'));

        } catch (\Exception $e) {
            Log::error('ContactMail error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo. Intenta de nuevo.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensaje enviado correctamente.',
        ]);
    }
}