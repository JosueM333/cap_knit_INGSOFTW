<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Bodega;
use App\Models\Producto;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $bodegas = Bodega::all();
        $productos = Producto::all();
        $transacciones = Transaccion::all();

        $query = Kardex::with(['bodega', 'producto', 'transaccion', 'ordenCompra', 'comprobante'])
            ->orderBy('KRD_FECHA', 'desc');

        // Filtros
        if ($request->filled('BOD_ID')) {
            $query->where('BOD_ID', $request->BOD_ID);
        }

        if ($request->filled('PRO_ID')) {
            $query->where('PRO_ID', $request->PRO_ID);
        }

        if ($request->filled('TRA_ID')) {
            $query->where('TRA_ID', $request->TRA_ID);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('KRD_FECHA', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('KRD_FECHA', '<=', $request->fecha_fin);
        }

        $movimientos = $query->paginate(20)->withQueryString();

        return view('kardex.index', compact('movimientos', 'bodegas', 'productos', 'transacciones'));
    }
}
