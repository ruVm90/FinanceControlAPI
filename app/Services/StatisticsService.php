<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatisticsService
{
    public function getSummary(): array
    {
        // Cogemos el usuario logueado
        $userId = Auth::id();

        // Fechas del mes actual
        $now = Carbon::now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $endOfCurrentMonth   = $now->copy()->endOfMonth();

        // Fechas del mes anterior
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

        // --- Mes actual ---
        $currentMonthExpenses = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])
            ->get();

        $totalCurrentMonth  = $currentMonthExpenses->sum('amount');
        $countCurrentMonth  = $currentMonthExpenses->count();

        // Media diaria: dividimos entre los días transcurridos del mes
        $daysElapsed = $now->day; // día del mes en que estamos
        $avgPerDay   = $daysElapsed > 0
            ? round($totalCurrentMonth / $daysElapsed, 2)
            : 0;

        // --- Mes anterior ---
        $totalLastMonth = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        // Diferencia en cantidad
        $amountDiff = round($totalCurrentMonth - $totalLastMonth, 2);

        // Diferencia en porcentaje (evitamos dividir entre 0)
        $percentageDiff = $totalLastMonth > 0
            ? round((($totalCurrentMonth - $totalLastMonth) / $totalLastMonth) * 100, 1)
            : null;

        return [
            'current_month' => [
                'total_spent'    => $totalCurrentMonth,
                'total_expenses' => $countCurrentMonth,
                'avg_per_day'    => $avgPerDay,
                'vs_last_month'  => [
                    'amount_diff'     => $amountDiff,
                    'percentage_diff' => $percentageDiff,
                ],
            ],
        ];
    }

    public function getByCategory(int $month, int $year): array
    {
        $userId = Auth::id();

        // Construimos las fechas a partir del mes y año recibidos
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Mismo periodo pero del mes anterior para la comparativa
        $startOfLastMonth = $startOfMonth->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $startOfMonth->copy()->subMonth()->endOfMonth();

        // Total gastado en el mes para calcular porcentajes
        $totalMonth = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Gastos del mes anterior agrupados por categoría
        $lastMonthByCategory = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        // Gastos del mes actual agrupados por categoría
        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as expenses_count')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Formateamos los resultados
        $data = $expenses->map(function ($expense) use ($totalMonth, $lastMonthByCategory) {

            $lastMonthTotal = $lastMonthByCategory->get($expense->category_id, 0);
            $amountDiff     = round($expense->total - $lastMonthTotal, 2);

            return [
                'category_id'        => $expense->category_id,
                'category_name'      => $expense->category->name,
                'total'              => round($expense->total, 2),
                'percentage_of_total' => $totalMonth > 0
                    ? round(($expense->total / $totalMonth) * 100, 1)
                    : 0,
                'expenses_count'     => $expense->expenses_count,
                'vs_last_month_diff' => $amountDiff,
            ];
        })->values()->toArray();

        return ['data' => $data];
    }


    public function getTrends(int $months): array
{
    $userId = Auth::id();

    // Generamos un array con los últimos N meses
    $data = collect(range(0, $months - 1))->map(function ($i) use ($userId) {

        // Carbon::now()->subMonths(0) = mes actual
        // Carbon::now()->subMonths(1) = mes anterior, etc.
        $date  = Carbon::now()->subMonths($i);
        $start = $date->copy()->startOfMonth();
        $end   = $date->copy()->endOfMonth();

        $total = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'month' => $date->format('Y-m'),       // "2026-03"
            'label' => $date->locale('es')->isoFormat('MMMM YYYY'), // "marzo 2026"
            'total' => round($total, 2),
        ];
    });

    // reverse() para que el array vaya del más antiguo al más reciente
    return ['data' => $data->reverse()->values()->toArray()];
}
}
