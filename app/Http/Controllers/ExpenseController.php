<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseDestroyRequest;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Http\Resources\ExpenseCollection;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        /** @var \Illuminate\Contracts\Auth\Guard $user */
        $user = auth();
        $expenses = Expense::where('user_id', $user->id())
            ->with('category')
            ->paginate(10);
        return new ExpenseCollection($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseStoreRequest $request)
    {
        /** @var \Illuminate\Contracts\Auth\Guard $user */
        $user = auth();
        $validated = $request->validated();
        $expense = Expense::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'user_id' => $user->id(),
        ]);
        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        /** @var \Illuminate\Contracts\Auth\Guard $user */
        $user = auth();
        if ($expense->user_id !== $user->id()) {
            return response()->json([
                'message' => 'Expense not found'
            ], 404);
        }
        $expense = $expense->load('category');
        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense)
    {

        $validated = $request->validated();

        $expense->update($validated);
        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseDestroyRequest $request, Expense $expense)
    {

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted',
            'id' => $expense->id

        ]);
    }

}
