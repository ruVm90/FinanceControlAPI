<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Services\StatisticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticController extends Controller
{
    // Inyeccion de dependencias
    public function __construct(private StatisticsService $service){}

    /**
     * - Calcula el total gastado en el mes actual
     *- Cuenta cuántos gastos ha registrado ese mes
     *- Divide el total entre los días transcurridos para sacar el promedio diario
     *- Compara el total con el mes anterior para sacar la diferencia en € y en %
     */
    public function summary(): JsonResponse
    {
       $data = $this->service->getSummary();
       return response()->json($data);
    }
}
