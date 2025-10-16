<?php

namespace App\Http\Controllers;

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

        $expenses = Expense::where('user_id', auth()->id())
            ->with('category')
            ->paginate(10);
        return response()->json($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:50|min:3',
            'description' => 'string|max:200',
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);
        $expense = Expense::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'user_id' => auth()->id(),
        ]);
        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(expense $expense)
    {
        //
    }
}
