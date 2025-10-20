<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title'=> $this->title,
            'description' => $this->description,
            'amount' => (float)$this->amount,
            'amount_formatted' => number_format($this->amount, 2, ',' , '.'),
            'amount_display' => '€' .  number_format($this->amount, 2, ',' , '.'),
            'created' => $this->created_at->diffForHumans(),
            'updated' => $this->updated_at->diffForHumans(),
            'category' => $this->when(
                $this->relationLoaded('category'),
                function () {
                    return [
                        'id' => $this->category->id,
                        'name' => $this->category->name,
                    ];
                }
            )
        ];
    }
}
