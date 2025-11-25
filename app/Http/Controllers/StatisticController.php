<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Json;

class statisticController extends Controller
{
    /**
     * Aqui creare el controlador que devuelva las estadisticas o el resumen
     * de los gastos del usuario.
     * Va a constar de dos endpoints:
     *    1- Devuelve los gastos del mes.
     *    2- Devuelve todas las estadisticas del mes
     */
    public function MonthStatistics(Request $request)
    {
        $user = auth();
         $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);
        $gastosMes = $this->obtenerGastosMes($validated->month, $validated->year);

        // Gasto mayor del mes
        $gastoMax = $gastosMes->sortByDesc('amount')->first();
        // Gasto menor del mes
        $gastoMin = $gastosMes->sortByAsc('amount')->first();
    }
    // Funcion que devuelve los gastos del mes elegido por el usuario
    private function obtenerGastosMes($mes, $anio)
    {
        // Usuario autenticado
        $user = Auth::user();
        // Obtengo el dia 1 del mes
        $start = Carbon::create($anio, $mes)->startOfMonth();
        // Obtengo el dia 1 del siguiente mes
        $end = Carbon::create($anio, $mes)->addMonth()->startOfMonth();
        // Hago la consulta buscando entre las dos fechas
        $gastosMes = Expense::where('user_id', $user->id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->get();
        return $gastosMes;
    }

    // Devuelve los gastos del mes en json
    public function gastosMensuales(Request $request)
    {

        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);
        $gastosMes = $this->obtenerGastosMes($validated->month, $validated->year);
        return response()->json($gastosMes, 200);
    }

}
