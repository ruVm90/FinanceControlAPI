<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->when(
                $this->collection->isNotEmpty(),
            [
                'total_expenses' => $this->collection->count(),
                'total_amount' => $this->collection->sum('amount'),
                'average_amount' => $this->collection->avg('amount'),
                'highest_expense' => $this->collection->max('amount'),
                'lowest_expense' => $this->collection->min('amount'),
                'first_expense_date' => $this->collection->first()?->created_at
            ])
        ];
    }
}
