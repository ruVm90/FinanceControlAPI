<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Category as ModelsCategory;
use App\Models\Expense;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $validated = $request->validated();
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $category = Auth::user()->categories()->create($validated);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show(Category $category){
        $expenses = Expense::where('category_id', $category->id)
                    ->paginate(10);
        return response()->json([
            'data' => [
                'category' => $category,
                'expenses' => $expenses
            ]
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, category $category)
    {
        $validated = $request->validated();
        $category->update($validated);

        return response()->json(
            [
                'message' => 'Category updated',
                'data' => $category
            ]
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted',
            'id' => $category->id
        ]);
    }
}
