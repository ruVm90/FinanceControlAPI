<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatisticsByCategoryRequest;
use App\Http\Requests\StatisticsTrendsRequest;
use App\Services\StatisticsService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticController extends Controller
{
    // Inyeccion de dependencias
    public function __construct(private StatisticsService $service) {}

    /**
     * Calcula el total gastado en el mes actual
     * Cuenta cuántos gastos ha registrado ese mes
     * Divide el total entre los días transcurridos para sacar el promedio diario
     * Compara el total con el mes anterior para sacar la diferencia en € y en %
     */
    public function summary(): JsonResponse
    {
        $data = $this->service->getSummary();
        return response()->json($data);
    }


    /**
     * Agrupa los gastos del mes por `category_id`
     * Suma el total de cada categoría
     * Calcula qué porcentaje representa cada una sobre el total del mes
     * Compara cada categoría con el mes anterior
     */
    public function byCategory(StatisticsByCategoryRequest $request): JsonResponse
    {
        // Si no vienen en la URL usamos el mes y año actual
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $data = $this->service->getByCategory($month, $year);

        return response()->json($data);
    }

    /**
     * Agrupa los gastos por mes"Devuelve los gastos agregados por mes para los últimos N meses,
     * listos para ser representados en un gráfico o dashboard"
     */
    public function trends(StatisticsTrendsRequest $request): JsonResponse
    {
        // Si no viene el parámetro usamos 6 meses por defecto
        $months = $request->integer('months', 6);

        $data = $this->service->getTrends($months);

        return response()->json($data);
    }
}
