<?php
namespace App\Http\Controllers\Root;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
 
class AuditController extends Controller
{
    /** Vista del panel */
    public function index(): View
    {
        return view('root.audit-viewer');
    }
 
    public function lines(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->format('Y-m-d'));
 
        // Validar formato para evitar path traversal
        abort_if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date), 422, 'Fecha inválida.');
 
        $file = storage_path("logs/audit/audit-{$date}.log");
 
        if (!file_exists($file)) {
            return response()->json([]);
        }
 
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 
        return response()->json(array_values($lines));
    }
}