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
}
